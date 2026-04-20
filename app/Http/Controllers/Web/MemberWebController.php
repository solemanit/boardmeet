<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberWebController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Member::with('createdBy')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($b) =>
                $b->where('name', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%")
                  ->orWhere('employee_id', 'like', "%$q%")
                  ->orWhere('designation', 'like', "%$q%")
            );
        }

        $members     = $query->paginate(15)->withQueryString();
        $departments = Member::distinct()->pluck('department')->filter()->sort()->values();

        return view('members.index', compact('members', 'departments'));
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create()
    {
        return view('members.create');
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        $member = Member::create($data);

        return redirect()->route('members.show', $member)
            ->with('success', 'Member added successfully.');
    }

    // ── Show ──────────────────────────────────────────────────────────────
    public function show(Member $member)
    {
        return view('members.show', compact('member'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    // ── Update ────────────────────────────────────────────────────────────
    public function update(StoreMemberRequest $request, Member $member)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        $member->update($data);

        return redirect()->route('members.show', $member)
            ->with('success', 'Member information updated successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────
    public function destroy(Member $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully.');
    }

    // ── Toggle Status ─────────────────────────────────────────────────────
    public function activate(Member $member)
    {
        $member->update(['status' => 'active', 'updated_by' => auth()->id()]);
        return back()->with('success', 'Member activated successfully.');
    }

    public function deactivate(Member $member)
    {
        $member->update(['status' => 'inactive', 'updated_by' => auth()->id()]);
        return back()->with('success', 'Member deactivated successfully.');
    }

    // ── Remove Photo ──────────────────────────────────────────────────────
    public function removePhoto(Member $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
            $member->update(['photo' => null]);
        }
        return back()->with('success', 'Photo removed successfully.');
    }
}
