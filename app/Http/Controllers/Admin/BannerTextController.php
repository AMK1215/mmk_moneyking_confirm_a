<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerText;
use App\Models\BannerTextAgent;
use App\Traits\AuthorizedCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerTextController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizedCheck;

    public function index()
    {
        $auth = auth()->user();
        $this->MasterAgentRoleCheck();
        $texts = $auth->hasPermission('master_access') ?
            BannerText::query()->master()->latest()->get() :
            BannerText::query()->agent()->latest()->get();

        return view('admin.banner_text.index', compact('texts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.banner_text.create');
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
            'text' => 'required',
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);
        $type = $request->type ?? 'single';
        if ($type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);

            $text = BannerText::create([
                'text' => $request->text,
            ]);
            BannerTextAgent::create([
                'banner_text_id' => $text->id,
                'agent_id' => $agentId,
            ]);
        } elseif ($type === 'all') {
            $text = BannerText::create([
                'text' => $request->text,
            ]);
            foreach ($user->agents as $agent) {
                BannerTextAgent::create([
                    'banner_text_id' => $text->id,
                    'agent_id' => $agent->id,
                ]);
            }
        }

        return redirect(route('admin.text.index'))->with('success', 'New Banner Text Created Successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }

        return view('admin.banner_text.show', compact('text'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }

        return view('admin.banner_text.edit', compact('text'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');

        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }

        $data = $request->validate([
            'text' => 'required',
        ]);
        $text->update($data);

        if ($request->type === 'single') {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $text->bankAgents()->delete();
            BannerTextAgent::create([
                'agent_id' => $agentId,
                'banner_text_id' => $text->id,
            ]);
        } elseif ($request->type === 'all') {
            foreach ($user->agents as $agent) {
                $text->bannerTextAgents()->updateOrCreate(
                    ['agent_id' => $agent->id],
                    ['bank_id' => $text->id]
                );
            }
        }

        return redirect(route('admin.text.index'))->with('success', 'Banner Text Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerText $text)
    {
        $this->MasterAgentRoleCheck();
        if (! $text) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }
        $text->delete();

        return redirect()->back()->with('success', 'Banner Text Deleted Successfully.');
    }
}
