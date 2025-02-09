<?php

namespace App\Http\Controllers\Admin\BannerAds;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerAds;
use App\Models\AdsBannerAgent;
use App\Models\BannerAdsAgent;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BannerAdsController extends Controller
{
    use AuthorizedCheck;

    /**
     * Display a listing of the resource.
     */
    use ImageUpload;

    public function index()
    {
        $auth = auth()->user();
        $this->MasterAgentRoleCheck();
        $banners = $auth->hasPermission('master_access') ?
            BannerAds::query()->master()->latest()->get() :
            BannerAds::query()->agent()->latest()->get();

        return view('admin.banner_ads.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.banner_ads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');

        $request->validate([
            'mobile_image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'desktop_image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
            'description' => 'nullable',
        ]);
        $type = $request->type ?? 'single';
        $mobile_image = $this->handleImageUpload($request->mobile_image, 'banners_ads');
        $desktop_image = $this->handleImageUpload($request->desktop_image, 'banners_ads');

        if ($type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $bannerAds = BannerAds::create([
                'mobile_image' => $mobile_image,
                'desktop_image' => $desktop_image,
                'description' => $request->description,
            ]);
            BannerAdsAgent::create([
                'banner_ads_id' => $bannerAds->id,
                'agent_id' => $agentId,
            ]);
        } elseif ($type === 'all') {
            $bannerAds = BannerAds::create([
                'mobile_image' => $mobile_image,
                'desktop_image' => $desktop_image,
                'description' => $request->description,
            ]);
            foreach ($user->agents as $agent) {
                BannerAdsAgent::create([
                    'banner_ads_id' => $bannerAds->id,
                    'agent_id' => $agent->id,
                ]);
            }
        }

        return redirect(route('admin.bannerAds.index'))->with('success', 'New Ads Banner Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);

        return view('admin.banner_ads.show', compact('adsbanner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($bannerAds)
    {
        $bannerAds = BannerAds::with('bannerAdsAgents')->where('id', $bannerAds)->first();

        $this->MasterAgentRoleCheck();
        if (! $bannerAds) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }

        return view('admin.banner_ads.edit', compact('bannerAds'));
    }

    public function update(Request $request, $bannerAds)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');
        $bannerAds = BannerAds::with('bannerAdsAgents')->where('id', $bannerAds)->first();

        if (! $bannerAds) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }

        $request->validate([
            'mobile_image' => 'image|max:2048',
            'desktop_image' => 'image|max:2048',
        ]);

        $this->deleteImagesIfProvided($bannerAds, $request);

        $this->UpdateData($request, $bannerAds);

        if ($request->type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $bannerAds->bannerAdsAgents()->delete();
            BannerAdsAgent::create([
                'agent_id' => $agentId,
                'banner_ads_id' => $bannerAds->id,
            ]);
        } elseif ($request->type === 'all') {
            foreach ($user->agents as $agent) {
                $bannerAds->bannerAdsAgents()->updateOrCreate(
                    ['agent_id' => $agent->id],
                    ['banner_ads_id' => $bannerAds->id]
                );
            }
        }

        return redirect(route('admin.bannerAds.index'))->with('success', 'Ads Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->MasterAgentRoleCheck();
        $bannerAds = BannerAds::find($id);

        if (! $bannerAds) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->handleImageDelete($bannerAds->mobile_image, 'banners_ads');
        $this->handleImageDelete($bannerAds->desktop_image, 'banners_ads');

        $bannerAds->delete();

        return redirect()->back()->with('success', 'Ads Banner Deleted.');
    }

    /**
     * Delete images if new ones are provided in the request.
     */
    private function deleteImagesIfProvided($bannerAds, Request $request): void
    {
        if ($request->hasFile('mobile_image')) {
            $this->handleImageDelete($bannerAds->mobile_image, 'banners');
        }

        if ($request->hasFile('desktop_image')) {
            $this->handleImageDelete($bannerAds->desktop_image, 'banners');
        }
    }

    /**
     * Prepare data for updating the banner.
     */
    private function UpdateData(Request $request, $bannerAds)
    {
        $updateData = ['description' => $request->input('description')];

        if ($request->hasFile('mobile_image')) {
            $updateData['mobile_image'] = $this->handleImageUpload($request->file('mobile_image'), 'banners_ads');
        } else {
            $updateData['mobile_image'] = $bannerAds->mobile_image;
        }

        if ($request->hasFile('desktop_image')) {
            $updateData['desktop_image'] = $this->handleImageUpload($request->file('desktop_image'), 'banners_ads');
        } else {
            $updateData['desktop_image'] = $bannerAds->desktop_image;
        }
        $adsbanner = $bannerAds->update($updateData);

        return $adsbanner;
    }
}
