<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Traits\WithConfirmation;
use Illuminate\Support\Facades\Hash;

class AdminUsers extends Component
{
    use WithPagination, WithConfirmation;

    public $search = '';
    public $isModalOpen = false;
    public $isEditingSelf = false; // Penanda apakah sedang edit profil sendiri

    // Form Fields
    public $user_id, $name, $email, $password, $role;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isEditingSelf = false;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['user_id', 'name', 'email', 'password', 'role', 'isEditingSelf']);
    }

    // FUNGSI BARU: Edit Profil Sendiri
    public function editMyProfile()
    {
        $user = auth()->user();
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';

        $this->isEditingSelf = true;
        $this->isModalOpen = true;
    }

    public function editUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->user_id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->password = '';

            $this->isEditingSelf = false;
            $this->isModalOpen = true;
        }
    }

    public function saveUser()
    {
        // Proteksi Backend: Jika edit diri sendiri, paksa role tetap seperti semula
        if ($this->isEditingSelf) {
            $this->role = auth()->user()->role;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'role' => 'required|in:admin,cashier,kitchen',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        $pesanSukses = $this->isEditingSelf ? 'Profil Anda berhasil diperbarui!' : ($this->user_id ? 'Data pengguna berhasil diperbarui!' : 'Pengguna baru berhasil ditambahkan!');

        session()->flash('success', $pesanSukses);
        $this->closeModal();
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user && $user->id === auth()->id()) {
            session()->flash('error', 'Aksi Ditolak: Anda tidak bisa menghapus akun Anda sendiri!');
            return;
        }

        if ($user) {
            $user->delete();
            session()->flash('success', 'Akun pengguna berhasil dihapus!');
        }
    }

    public function render()
    {
        $users = User::where('id', '!=', auth()->id())
                     ->where(function($query) {
                         $query->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                     })
                     ->latest()
                     ->paginate(10);

        return view('admin.admin-users', compact('users'))
               ->layout('components.layouts.app');
    }
}
