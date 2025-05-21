@extends('User.layout.user_layout')

@section('title')
    Purchase History | Easy Learning
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .table-responsive img {
            width: 70px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .alert-info {
            text-align: center;
        }
        .details-control {
            cursor: pointer;
            text-align: center;
        }
        .details-control::before {
            content: '\f055'; /* FontAwesome plus icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #007bff;
        }
        tr.shown .details-control::before {
            content: '\f056'; /* FontAwesome minus icon */
        }
        .invoice-subtable {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .invoice-subtable table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-subtable th, .invoice-subtable td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        @media (max-width: 991px) {
            .table-responsive img {
                width: 50px;
                height: 30px;
            }
            .table th, .table td {
                font-size: 0.9rem;
            }
            .invoice-subtable {
                padding: 10px;
            }
        }
    </style>
@endpush

@section('userdashboard')
<div class="page-content">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Purchase History</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Your Purchase History</h4>
            @if ($purchases->isEmpty())
                <div class="alert alert-info" role="alert">
                    You haven't purchased any courses yet. 
                    <a href="{{ route('course.list') }}" class="text-primary">Explore Courses</a>
                </div>
            @else
                <div class="table-responsive">
                    <table id="purchaseTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="details-control"></th>
                                <th>Sl</th>
                                <th>Image</th>
                                <th>Course Name</th>
                                <th>Instructor</th>
                                <th>Category</th>
                                <th>Price Paid</th>
                                <th>Purchase Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $key => $purchase)
                                <tr data-purchase-id="{{ $purchase->id }}">
                                    <td class="details-control"></td>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <img src="{{ $purchase->course_image ? asset('upload/course_images/thumbnail/' . $purchase->course_image) : asset('upload/no_image.jpg') }}" 
                                             alt="{{ $purchase->course_title }}" 
                                             class="img-fluid"
                                             onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                                    </td>
                                    <td>{{ $purchase->course_title }}</td>
                                    <td>{{ $purchase->instructor_name ?? 'N/A' }}</td>
                                    <td>{{ $purchase->category_name ?? 'Uncategorized' }}</td>
                                    <td>
                                        ${{ number_format($purchase->price, 2) }}
                                        @if ($purchase->discount_amount > 0)
                                            <br><small class="text-muted"><s>Original: ${{ number_format($purchase->original_price, 2) }}</s></small>
                                        @endif
                                    </td>
                                    <td>{{ $purchase->purchase_date ? $purchase->purchase_date->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ url('mycourses/learn/' . $purchase->course_id . '/' . Str::slug($purchase->course_title)) }}" 
                                               class="btn btn-primary btn-sm" 
                                               title="View Course">
                                                <i class="bx bx-play"></i> View Course
                                            </a>
                                            @if ($purchase->invoice)
                                                <a href="{{ route('invoice.download', $purchase->invoice_id) }}" 
                                                   class="btn btn-info btn-sm download-invoice" 
                                                   title="Download Invoice">
                                                    <i class="bx bx-download"></i> Download
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        No purchase history found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to format invoice details as a sub-table
            function formatInvoiceDetails(invoice) {
                if (!invoice) {
                    return '<div class="invoice-subtable">No invoice available</div>';
                }

                let items = [];
                try {
                    items = typeof invoice.items === 'string' ? JSON.parse(invoice.items) : invoice.items;
                } catch (e) {
                    console.error('Error parsing invoice items:', e);
                }

                let itemsHtml = '';
                if (Array.isArray(items) && items.length > 0) {
                    itemsHtml = items.map(item => `
                        <tr>
                            <td>${item.course_title || 'Unknown Course'}</td>
                            <td>$${Number(item.price || 0).toFixed(2)}</td>
                            <td>$${Number(item.discount || 0).toFixed(2)}</td>
                        </tr>
                    `).join('');
                } else {
                    itemsHtml = '<tr><td colspan="3">No items listed</td></tr>';
                }

                return `
                    <div class="invoice-subtable">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Issued Date</th>
                                    <th>Subtotal</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${invoice.invoice_number || 'N/A'}</td>
                                    <td>${invoice.issued_at ? new Date(invoice.issued_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A'}</td>
                                    <td>$${Number(invoice.subtotal || 0).toFixed(2)}</td>
                                    <td>$${Number(invoice.discount || 0).toFixed(2)}</td>
                                    <td>$${Number(invoice.total || 0).toFixed(2)}</td>
                                    <td>${invoice.payment_method || 'N/A'}</td>
                                </tr>
                            </tbody>
                        </table>
                        <h6>Items</h6>
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Title</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>`;
            }

            // Initialize DataTable
            const table = $('#purchaseTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                language: {
                    search: "Search purchases:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ purchases",
                    paginate: { previous: "Previous", next: "Next" }
                },
                rowReorder: false,
                columnDefs: [
                    { orderable: false, targets: [0, 2, 8] }, // Disable sorting on details, image, action
                    { className: 'details-control', targets: 0 }
                ]
            });

            // Handle click on details-control to show/hide invoice details
            $('#purchaseTable tbody').on('click', 'td.details-control', function() {
                const tr = $(this).closest('tr');
                const row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    const purchaseId = tr.data('purchase-id');
                    const purchase = table.row(tr).data();
                    // Assume invoice data is passed via a hidden data attribute or fetch dynamically
                    const invoice = tr.find('.action-buttons').data('invoice') || null;
                    row.child(formatInvoiceDetails(invoice)).show();
                    tr.addClass('shown');
                }
            });

            // Pass invoice data to rows (since DataTables doesn't render Blade data directly)
            @foreach ($purchases as $purchase)
                $(`tr[data-purchase-id="${{ $purchase->id }}"] .action-buttons`).data('invoice', @json($purchase->invoice));
            @endforeach
        });
    </script>
@endpush