<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Promotion;
use App\Models\PromotionAgent;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
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
        $promotions = $auth->hasPermission('master_access') ?
            Promotion::query()->master()->latest()->get() :
            Promotion::query()->agent()->latest()->get();

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.promotions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');

        // Validate the request
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);

        $type = $request->type ?? 'single';
        $filename = $this->handleImageUpload($request->image, 'promotions');

        $type = $request->type ?? 'single';
        if ($type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);
            $promotion = Promotion::create([
                'image' => $filename,
                'description' => $request->description,
            ]);
            PromotionAgent::create([
                'promotion_id' => $promotion->id,
                'agent_id' => $agentId,
            ]);
        } elseif ($type === 'all') {
            $promotion = Promotion::create([
                'image' => $filename,
                'description' => $request->description,
            ]);
            foreach ($user->agents as $agent) {
                PromotionAgent::create([
                    'promotion_id' => $promotion->id,
                    'agent_id' => $agent->id,
                ]);
            }
        }

        return redirect(route('admin.promotions.index'))->with('success', 'New Promotions Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }

        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }

        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }

        $this->deleteImagesIfProvided($promotion, $request);

        $this->UpdateData($request, $promotion);

        if ($request->type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $promotion->promotionAgents()->delete();
            PromotionAgent::create([
                'agent_id' => $agentId,
                'promotion_id' => $promotion->id,
            ]);
        } elseif ($request->type === 'all') {
            foreach ($user->agents as $agent) {
                $promotion->promotionAgents()->updateOrCreate(
                    ['agent_id' => $agent->id],
                    ['promotion_id' => $promotion->id]
                );
            }
        }

        return redirect(route('admin.promotions.index'))->with('success', 'Promotion Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $this->MasterAgentRoleCheck();
        if (! $promotion) {
            return redirect()->back()->with('error', 'Promotion Not Found');
        }
        $this->handleImageDelete($promotion->image, 'promotions');
        $promotion->delete();

        return redirect()->back()->with('success', 'Promotion Deleted.');
    }

    /**
     * Delete images if new ones are provided in the request.
     */
    private function deleteImagesIfProvided(Promotion $promotion, Request $request): void
    {
        if ($request->hasFile('image')) {
            $this->handleImageDelete($promotion->image, 'promotions');
        }
    }

    /**
     * Prepare data for updating the banner.
     */
    private function UpdateData(Request $request, Promotion $promotion): Promotion
    {
        $updateData = ['description' => $request->input('description')];

        if ($request->hasFile('image')) {
            $updateData['image'] = $this->handleImageUpload($request->file('image'), 'promotions');
        } else {
            $updateData['image'] = $promotion->image;
        }
        $promotion->update($updateData);

        return $promotion;
    }
}
