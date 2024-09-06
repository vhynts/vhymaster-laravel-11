@extends('zLayouts.main')

@push('script_head')
    {{-- <link rel="stylesheet" href="{{ asset('assets/datatables/datatables.min.css') }}"> --}}
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">

    <style>
        .input-light .select2-selection--single {
            background-color: var(--vz-light);
            border: none
        }

        .dataTables_processing {
            background-color: transparent;
            /* Make background transparent */
            color: black;
            /* Text color */
            border: none;
            /* Remove border */
            box-shadow: none;
            /* Remove box shadow */
        }
    </style>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Pengguna</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Pengaturan</a></li>
                        <li class="breadcrumb-item active">Pengguna</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="orderList">
                <div class="card-header border-0">
                    <div class="row align-items-center gy-3">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Data Pengguna</h5>
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex gap-1 flex-wrap">
                                <button id="toggleFilter" class="btn btn-info waves-effect"><i class=" bx bx-filter"></i>
                                    Filter</button>
                                <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal"
                                    id="create-btn" data-bs-target="#penggunaTambahModal"><i
                                        class="ri-add-line align-bottom me-1"></i> Tambah</button>
                                <!-- <button type="button" class="btn btn-info"><i class="ri-file-download-line align-bottom me-1"></i> Import</button> -->
                                <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- MODAL ADD USER --}}

                <div class="modal fade" id="penggunaTambahModal" tabindex="-1" aria-labelledby="exampleModalgridLabel"
                    aria-modal="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="exampleModalgridLabel">Tambah Pengguna</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="penggunaTambahForm" name="penggunaTambahForm">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="nama" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="nama" name="nama"
                                                    placeholder="Input nama lengkap">
                                                <div id="error-nama" class="invalid-feedback"></div> <!-- Tempat error -->
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="email" class="form-label">Email</label>
                                                <input type="text" class="form-control" id="email" name="email"
                                                    placeholder="Input email">
                                                <div id="error-email" class="invalid-feedback"></div> <!-- Tempat error -->
                                            </div>
                                        </div><!--end col-->


                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div><!--end col-->

                                        <div class="form-group">
                                            <label for="peran">Role:</label>
                                            <select name="peran" id="peran" class="form-control">
                                                <option value="">-Pilih Role-</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">
                                                        {{ str_replace('_', ' ', $role->name) }}</option>
                                                @endforeach
                                            </select>
                                            <div id="error-peran" class="invalid-feedback"></div> <!-- Tempat error -->
                                        </div>

                                    </div><!--end row-->



                            </div>
                            <div class="modal-footer">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary" id="save-tambah-btn">Simpan</button>
                                    <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                                </div>
                            </div>

                        </div>
                        </form>
                    </div>
                </div>

                {{-- END MODAL ADD USER --}}

                {{-- MODAL EDIT USER --}}

                <div class="modal fade" id="penggunaUpdateModal" tabindex="-1" aria-labelledby="exampleModalgridLabel"
                    aria-modal="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="exampleModalgridLabel">Update Pengguna</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="penggunaUpdateForm" name="penggunaUpdateForm">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="editUserId">
                                    <div class="row g-3">
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="editNama" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="editNama" name="nama"
                                                    placeholder="Input nama lengkap">
                                                <div id="edit-error-nama" class="invalid-feedback"></div>
                                                <!-- Tempat error -->
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="editEmail" class="form-label">Email</label>
                                                <input type="text" class="form-control" id="editEmail" name="email"
                                                    placeholder="Input email">
                                                <div id="edit-error-email" class="invalid-feedback"></div>
                                                <!-- Tempat error -->
                                            </div>
                                        </div><!--end col-->


                                        <div class="col-xxl-12">
                                            <div>
                                                <label for="editStatus" class="form-label">Status</label>
                                                <select class="form-control" id="editStatus" name="status">
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div><!--end col-->

                                        <div class="form-group">
                                            <label for="editPeran">Role:</label>
                                            <select name="peran" id="editPeran" class="form-control">
                                                <option value="">-Pilih Role-</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ isset($userRole) && $userRole === $role->name ? 'selected' : '' }}>
                                                        {{ str_replace('_', ' ', $role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="edit-error-peran" class="invalid-feedback"></div>
                                            <!-- Tempat error -->
                                        </div>

                                    </div><!--end row-->



                            </div>
                            <div class="modal-footer">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary" id="save-update-btn">Simpan</button>
                                    <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                                </div>
                            </div>

                        </div>
                        </form>
                    </div>
                </div>

                {{-- END MODAL EDIT USER --}}

                <div class="card-body border border-dashed border-end-0 border-start-0 row mb-2" id="filterForm"
                    style="display: none">

                    <div class="col-xxl-8 col-md-8 mb-2">
                        <div class="search-box">
                            <input type="text" class="form-control search bg-light border-light" name="customSearch"
                                id="customSearch" placeholder="Cari nama atau lainnya...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-md-4 mb-2">
                        <div class="input-light">
                            <select class="form-control" name="statusFilter" id="statusFilter" required>
                                <option value="" class="text-muted">Semua Status</option>
                                <option value="1" class="text-muted">Aktif</option>
                                <option value="0" class="text-muted">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1"
                                    role="tab" aria-selected="true">
                                    <i class="ri-account-circle-line me-1 align-bottom"></i> Semua<span
                                        class="badge bg-danger align-middle ms-1">{{ $jumlahPengguna }}</span>
                                </a>
                            </li>

                        </ul>

                        <div class="table-responsive">
                            <table id="pengguna-tabel" class="table nowrap align-middle" style="width:100%">
                                <thead class="text-muted table-light">
                                    <tr class="text-uppercase">
                                        <th>#</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Role</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="">

                                </tbody>
                            </table>

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

        <!--datatable js-->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        {{-- <script src="{{ asset('assets/datatables/datatables.min.js') }}"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>



        <script>
            $(document).ready(function() {


                $("#statusFilter").select2({});


            });
        </script>


        <script>
            $(document).ready(function() {
                $("#status").select2({
                    dropdownParent: $("#penggunaTambahModal")
                });


                $("#peran").select2({
                    dropdownParent: $("#penggunaTambahModal")
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $("#editStatus").select2({
                    dropdownParent: $("#penggunaUpdateModal")
                });


                $("#editPeran").select2({
                    dropdownParent: $("#penggunaUpdateModal")
                });
            });
        </script>



        <script type="text/javascript">
            $(document).ready(function() {
                // Initialize the DataTable and assign it to a variable
                var table = $('#pengguna-tabel').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    ajax: {
                        url: '{{ route('usersDataAjax') }}',
                        type: 'POST',
                        data: function(d) {
                            d._token = '{{ csrf_token() }}';
                            d.customSearch = $('#customSearch').val(); // Add custom search value
                            d.statusFilter = $('#statusFilter').val(); // Add status filter value
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'roles',
                            name: 'roles'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            searchable: false,
                            render: function(data) {
                                if (data === 1) {
                                    return '<span class="badge bg-success">Aktif</span>';
                                } else if (data === 0) {
                                    return '<span class="badge bg-danger">Tidak Aktif</span>';
                                } else {
                                    return '<span class="badge bg-secondary">Unknown</span>';
                                }
                            }
                        },
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
            <button type="button" class="btn btn-sm btn-primary" onclick="editUser(${data})">
                <i class="ri-edit-2-fill"></i>
            </button>
            <form id="delete-form-${data}" action="/users/${data}/delete" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${data})">
                    <i class="ri-delete-bin-fill"></i>
                </button>
            </form>
            <button type="button" class="btn btn-sm btn-warning" onclick="resetPassword(${data})">
                <i class="ri-rotate-lock-fill"></i>
            </button>
        `;
                            }
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            width: '5%'
                        } // Ensure this aligns with your style
                    ],
                    language: {
                        processing: "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
                    }
                });

                // Reload DataTable on custom search input change
                $('#customSearch').on('keyup', function() {
                    table.ajax.reload();
                });

                // Reload DataTable on status filter change
                $('#statusFilter').on('change', function() {
                    table.ajax.reload();
                });
            });
        </script>


        <script>
            $(document).ready(function() {
                $('#toggleFilter').click(function() {
                    var $filterForm = $('#filterForm');
                    var $button = $(this);

                    // Menampilkan atau menyembunyikan form filter dengan efek slide
                    $filterForm.slideToggle(300, function() {
                        // Callback untuk memperbarui teks tombol setelah animasi selesai
                        var isVisible = $filterForm.is(':visible');
                        var buttonText = isVisible ?
                            '<i class="bx bx-x"></i> Filter' :
                            '<i class="bx bx-filter"></i> Filter';
                        $button.html(buttonText);
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#penggunaTambahForm').on('submit', function(e) {
                    e.preventDefault();
                    var $submitButton = $('#save-tambah-btn');

                    // Disable the submit button
                    $submitButton.prop('disabled', true);

                    // Clear any existing errors
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');

                    // Ajax request
                    $.ajax({
                        url: "{{ route('userStore') }}", // Adjusted route name assuming 'user.store' is correct
                        method: 'POST',
                        data: $(this).serialize(),

                        success: function(response) {
                            alert(response.success); // Show success message
                            window.location.href = response
                                .redirect_url; // Redirect to the specified URL


                        },

                        error: function(response) {
                            var errors = response.responseJSON.errors;
                            for (var key in errors) {
                                // Disable the submit button
                                $submitButton.prop('disabled', false);

                                if (errors.hasOwnProperty(key)) {
                                    var input = $('#' + key);
                                    var errorElement = $('#error-' + key);
                                    input.addClass('is-invalid');
                                    errorElement.text(errors[key][0]);
                                }
                            }
                        }
                    });
                });
            });
        </script>

        {{-- <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan bisa mengembalikan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form jika dikonfirmasi
                        document.getElementById(`delete-form-${id}`).submit();


                    }
                });
            }
        </script> --}}

        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan bisa mengembalikan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form via AJAX if confirmed
                        $.ajax({
                            url: `/users/${id}/delete`, // Form action URL
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}' // CSRF token
                            },
                            success: function(response) {
                                // Show success message with SweetAlert
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The user has been deleted.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Optionally reload the DataTable or the page
                                    $('#pengguna-tabel').DataTable().ajax.reload();
                                });
                            },
                            error: function(xhr) {
                                // Handle errors if needed
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an error deleting the user.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }
        </script>

        <script>
            function resetPassword(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Password akan di-reset menjadi default!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Lakukan AJAX untuk reset password
                        $.ajax({
                            url: "{{ route('resetPassword') }}", // Route Laravel untuk reset password
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Terjadi kesalahan saat mereset password.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghubungi server.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }
        </script>

        <script>
            function editUser(id) {
                // Fetch user data via AJAX
                $.ajax({
                    url: `/users/${id}/edit`, // URL to fetch user data
                    method: 'GET',
                    success: function(response) {
                        // Populate the form fields with the user data
                        $('#editUserId').val(response.id);
                        $('#editNama').val(response.name);
                        $('#editEmail').val(response.email);

                        // Set the status and trigger change event
                        $('#editStatus').val(response.is_active).change();

                        // Set the role and trigger change event
                        $('#editPeran').val(response.role).change(); // Assuming role is a single value

                        // Open the modal
                        $('#penggunaUpdateModal').modal('show');
                    },
                    error: function() {
                        alert('Error fetching user data.');
                    }
                });
            }

            $('#penggunaUpdateForm').submit(function(e) {
                e.preventDefault();
                var $submitButton = $('#save-update-btn');

                // Disable the submit button
                $submitButton.prop('disabled', true);

                var id = $('#editUserId').val();
                var formData = $(this).serialize();
                console.log('Form submitted with data:', formData);
                $.ajax({
                    url: '/users/' + id + '/update',
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        // alert(response.success);
                        // window.location.reload(); // Refresh the page after update

                        // Hide the modal
                        $('#penggunaUpdateModal').modal('hide');


                        // Optionally, show a success message
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Optionally reload the DataTable or the page
                            $('#pengguna-tabel').DataTable().ajax.reload();
                        });



                        // Re-enable the submit button
                        $submitButton.prop('disabled', false);
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        console.log('Form submitted with eror:', errors);
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text(''); // Clear previous error messages
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                // Re-enable the submit button
                                $submitButton.prop('disabled', false);

                                // var input = $('#edit-' + key);
                                var inputId = key.charAt(0).toUpperCase() + key.slice(
                                    1); // Capitalize first letter
                                var input = $('#edit' +
                                    inputId); // Assuming IDs in your HTML start with "edit"
                                var errorElement = $('#edit-error-' + key);
                                input.addClass('is-invalid');
                                errorElement.text(errors[key][0]);
                            }
                        }
                    }
                });


            });
        </script>
    @endpush
