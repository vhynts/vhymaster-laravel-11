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
        <div class="col-lg-12">
            <div class="card" id="peranList">
                <div class="card-header border-bottom-dashed">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">Data Roles</h5>
                            </div>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="{{ route('kelolaRolesIndex') }}" class="btn btn-primary add-btn"
                                    id="kelola-btn"></i> Kelola Peran</a>
                                <button type="button" class="btn btn-success add-btn" id="create-btn"
                                    data-bs-toggle="modal" data-bs-target="#modalTambahPeran"><i
                                        class="ri-add-line align-bottom me-1"></i> Tambah</button>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="peran-tabel" class="table wrap align-middle" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Nama</th>
                                                <th>Guard</th>
                                                <th>Tgl Buat</th>
                                                <th>Tgl Ubah</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->


    {{-- TEMPAT MODAL --}}

    {{-- MODAL TAMBAH PERAN --}}
    <div class="modal fade" id="modalTambahPeran" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalgridLabel">Tambah Roles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahPeran" action="{{ route('rolesStore') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namaPeran" class="form-label">Nama Roles</label>
                                    <input type="text" class="form-control" id="namaPeran" name="namaPeran"
                                        placeholder="Nama roles">
                                    <div id="error-namaPeran" class="invalid-feedback"></div> <!-- Tempat error -->
                                </div>
                            </div><!--end col-->


                        </div><!--end row-->

                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- END MODAL TAMBAH PERAN --}}

    {{-- MODAL EDIT PERAN --}}
    <div class="modal fade" id="modalEditPeran" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalgridLabel">Edit Peran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditPeran" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-xxl-12">
                                <div>
                                    <label for="namaPeranEdit" class="form-label">Nama Roles</label>
                                    <input type="text" class="form-control" id="namaPeranEdit" name="namaPeranEdit"
                                        placeholder="Nama roles">
                                    <div id="edit-error-namaPeranEdit" class="invalid-feedback"></div>
                                </div>
                            </div>

                        </div>

                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- END MODAL EDIT PERAN --}}




    {{-- END TEMPAT MODAL --}}
@endsection

@push('script_body')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/datatables.min.js') }}"></script>

    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>



    <script type="text/javascript">
        $(document).ready(function() {
            $('#peran-tabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('rolesDataAjax') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'guard_name',
                        name: 'guard_name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'id', // Use the 'id' or any unique field
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
         <button type="button" class="btn btn-sm btn-primary edit-role" data-id="${data}" data-name="${row.name}" data-guard="${row.guard_name}" data-toggle="modal" data-target="#modalEditPeran"> <i class="ri-edit-2-fill"></i></button>
            <form action="{{ url('/lainnya/peran/${data}/delete') }}" method="POST" class="delete-form" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger delete-button"> <i class="ri-delete-bin-fill"></i> </button>
            </form>
        `;
                        }
                    }

                ],
                columnDefs: [{
                        targets: 0,
                        width: '5%'
                    } // Ensure this aligns with your style
                ]
            });
        });

        $('#formTambahPeran').on('submit', function(e) {
            e.preventDefault(); // Mencegah pengiriman form secara default

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var formData = form.serialize();

            // Kosongkan pesan error sebelumnya
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    // Jika berhasil, tutup modal dan reload halaman
                    $('#modalTambahPeran').modal('hide');
                    location.reload(); // Reload halaman untuk melihat data yang baru
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // 422 artinya Unprocessable Entity (validasi gagal)
                        var errors = xhr.responseJSON.errors;
                        if (errors.namaPeran) {
                            $('#namaPeran').addClass('is-invalid');
                            $('#error-namaPeran').text(errors.namaPeran[0]);
                        }
                    }
                }
            });
        });

        $(document).on('click', '.edit-role', function() {
            var roleId = $(this).data('id');
            var roleName = $(this).data('name');


            // Set the form action to the update route
            $('#formEditPeran').attr('action', `/lainnya/peran/${roleId}/update`);

            // Populate the modal fields with role data
            $('#namaPeranEdit').val(roleName);


            // Show the modal (this might be necessary if the modal doesn't trigger automatically)
            $('#modalEditPeran').modal('show');
        });

        $('#formEditPeran').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var formData = form.serialize();

            $('.invalid-feedback').text(''); // Clear previous errors
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    $('#modalEditPeran').modal('hide');
                    location.reload(); // Reload the page to see the updated data
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Unprocessable Entity (validation error)
                        var errors = xhr.responseJSON.errors;
                        if (errors.namaPeranEdit) {
                            $('#namaPeranEdit').addClass('is-invalid');
                            $('#edit-error-namaPeranEdit').text(errors.namaPeranEdit[0]);
                        }

                    }
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();

            var form = $(this).closest('form'); // Get the form element

            Swal.fire({
                title: 'Apa anda yakin?',
                text: "Anda tidak dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if confirmed
                }
            });
        });
    </script>
@endpush
