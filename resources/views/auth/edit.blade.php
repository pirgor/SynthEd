@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-4">
    <div class="card shadow-lg rounded-4 w-75">
        <div class="card-header text-white text-center fw-bold fs-4" style="background-color: #1a3a2f;">
            User Account Settings
        </div>
        <div class="card-body d-flex flex-column align-items-center">
            
            {{-- Profile Picture --}}
            <div class="mb-3">
                @if($user->profile_picture)
                    <img id="previewImage" src="{{ asset('storage/'.$user->profile_picture) }}" 
                         class="rounded-circle" 
                         width="150" height="150" 
                         style="object-fit: cover;">
                @else
                    <div id="previewPlaceholder" class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                         style="width:150px; height:150px;">
                        <span class="text-white-50">No Photo</span>
                    </div>
                @endif
            </div>

            {{-- Profile Form --}}
            <form id="profileForm" action="{{ route('auth.update') }}" method="POST" enctype="multipart/form-data" class="w-75">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">NAME</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">EMAIL</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">PASSWORD</label>
                    <input type="password" name="password" class="form-control" placeholder="***********" disabled>
                </div>

                <div class="mb-3 d-none" id="uploadWrapper">
                    <label class="form-label fw-bold">Profile Picture</label>
                    <input type="file" name="profile_picture" class="form-control" id="profileInput" accept="image/*">
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    {{-- Toggle Buttons --}}
                    <button type="button" id="editBtn" class="btn btn-success px-4">Edit Profile</button>
                    <button type="submit" id="saveBtn" class="btn btn-primary px-4 d-none">Save Changes</button>
                    <button type="button" id="cancelBtn" class="btn btn-danger px-4 d-none">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script to toggle edit mode --}}
<script>
document.getElementById("editBtn").addEventListener("click", function() {
    document.querySelectorAll("#profileForm input").forEach(el => el.disabled = false);
    document.getElementById("uploadWrapper").classList.remove("d-none");
    document.getElementById("editBtn").classList.add("d-none");
    document.getElementById("saveBtn").classList.remove("d-none");
    document.getElementById("cancelBtn").classList.remove("d-none");
});

document.getElementById("cancelBtn").addEventListener("click", function() {
    document.querySelectorAll("#profileForm input").forEach(el => el.disabled = true);
    document.getElementById("uploadWrapper").classList.add("d-none");
    document.getElementById("editBtn").classList.remove("d-none");
    document.getElementById("saveBtn").classList.add("d-none");
    document.getElementById("cancelBtn").classList.add("d-none");
    document.getElementById("profileInput").value = ""; // reset file input
});

// Preview new profile picture before save
document.getElementById("profileInput")?.addEventListener("change", function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            let img = document.getElementById("previewImage");
            let placeholder = document.getElementById("previewPlaceholder");
            if (!img) {
                img = document.createElement("img");
                img.id = "previewImage";
                img.className = "rounded-circle";
                img.style.objectFit = "cover";
                img.width = 150;
                img.height = 150;
                placeholder.replaceWith(img);
            }
            img.src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
