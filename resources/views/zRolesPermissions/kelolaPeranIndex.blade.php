@extends('zLayouts.main')

@push('script_head')
    <link rel="stylesheet" href="{{ asset('assets/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Roles</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Lain-lain</a></li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-8">
            <div class="card" id="peranList">
                <div class="card-header border-bottom-dashed">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0"> Kelola Roles</h5>
                            </div>
                        </div>



                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="card-body">
                                <form action="{{ route('kelolaRolesUpdate') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group col-lg-4 mb-3">
                                        <label for="update_role_id">Pilih Roles:</label>
                                        <select name="role_id" id="update_role_id" class="form-control mb-3" required>
                                            <option value="" disabled selected>-Pilih Role-</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ str_replace('_', ' ', $role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label for="update_permissions">Daftar Permissions:</label>
                                        <div id="permissions-container">

                                            <!-- Checkboxes will be populated dynamically here -->
                                        </div>
                                    </div>

                                    {{-- <button type="submit" class="btn btn-primary">Update Permissions</button> --}}
                                    <!-- The button is hidden initially -->
                                    <button type="submit" class="btn btn-primary mt-3" id="update-permissions-btn"
                                        style="display: none;">Update
                                        Permissions</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection

@push('script_body')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#update_role_id").select2({

            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#update_role_id').change(function() {
                var roleId = $(this).val();

                // Clear existing permissions
                $('#permissions-container').empty();

                if (roleId) {
                    $.ajax({
                        url: '/others/roles/' + roleId +
                            '/permissions', // Adjust the URL as necessary
                        type: 'GET',
                        success: function(data) {

                            if (data.permissions.length > 0) {
                                // Append the 'Check All' checkbox to the container
                                $('#permissions-container').append(
                                    `<input type="checkbox" class="form-check-input mb-3" name="select-all" id="select-all" />
        <label for="select-all" class="form-check-label"> Check All</label>`
                                );

                                $.each(data.permissions, function(index, permission) {
                                    var isChecked = permission.assigned ? 'checked' :
                                        '';
                                    $('#permissions-container').append(

                                        `<div class="form-check m-2">
                                            <input type="checkbox" name="permissions[]" value="${permission.name}"
                                                class="form-check-input" id="permission-${permission.id}" ${isChecked}>
                                            <label class="form-check-label" for="permission-${permission.id}">
                                                ${permission.name.replace('_', ' ')}
                                            </label>
                                        </div>`
                                    );
                                });
                                // Show the update permissions button
                                $('#update-permissions-btn').show();
                            } else {
                                $('#permissions-container').append(
                                    '<p>Tidak ada izin yang tersedia.</p>');
                            }
                        }
                    });
                } else {
                    // Hide the update permissions button if no role is selected
                    $('#update-permissions-btn').hide();
                }
            });
        });
    </script>

    <script>
        // Event listener for 'Check All' checkbox
        $(document).on('change', '#select-all', function() {
            var isChecked = $(this).is(':checked');
            $('#permissions-container input[name="permissions[]"]').prop('checked', isChecked);
        });
    </script>
@endpush
