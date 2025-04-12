@extends('layouts.agent')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Page Heading -->
    <h1 class="text-3xl font-bold text-white mb-6">Meter Reading History</h1>

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="mt-4 bg-green-900 border-l-4 border-green-500 text-green-300 p-4 rounded-lg shadow-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 bg-red-900 border-l-4 border-red-500 text-red-300 p-4 rounded-lg shadow-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Customer Selection -->
    <div class="bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-700 mb-6">
        <form id="customer-select-form" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="customer_select" class="block text-sm font-medium text-gray-300 mb-1">Select Customer</label>
                <select name="customer_id" id="customer_select" 
                        class="block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white">
                    <option value="">Select a customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-4">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    View History
                </button>
                <button type="button" id="new-reading-btn"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Submit New Reading
                </button>
            </div>
        </form>
    </div>

    <!-- Reading History Table -->
    <div class="bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700" id="readings-table">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Previous Reading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Current Reading</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Units Used</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    <!-- Table content will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Submit Reading Modal -->
    <div class="modal fade" id="submitReadingModal" tabindex="-1" aria-labelledby="submitReadingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-gray-800 border border-gray-700">
                <div class="modal-header bg-gray-700 border-b border-gray-600 p-4">
                    <h5 class="modal-title text-lg font-semibold text-white" id="submitReadingModalLabel">Submit New Reading</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="reading-form" action="{{ route('agent.submit_reading') }}" method="POST">
                    @csrf
                    <div class="modal-body p-6">
                        <div id="msg" class="mb-4"></div>
                        <input type="hidden" name="customer_id" id="modal_customer_id">
                        <input type="hidden" name="previous_reading" id="modal_previous_reading">
                        <p class="mb-4"><strong class="text-white">Customer:</strong> <span id="customer-name" class="font-medium text-gray-300"></span></p>
                        <p class="mb-4"><strong class="text-white">Previous Reading:</strong> <span id="prev-reading" class="font-medium text-gray-300"></span></p>
                        <div>
                            <label for="current_reading" class="block text-sm font-medium text-gray-300 mb-1">Current Reading</label>
                            <input type="number" name="current_reading" id="current_reading" 
                                   class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-white @error('current_reading') border-red-500 @enderror" 
                                   required min="0" placeholder="Enter current reading">
                            @error('current_reading')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Units Used (Calculated)</label>
                            <div id="units-used" class="font-medium text-gray-300">-</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-gray-700 border-t border-gray-600 p-4">
                        <button type="submit" id="submit-button" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>


@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    let readingsTable = $('#readings-table').DataTable({
        "order": [[0, "desc"]],
        "paging": true,
        "searching": true,
        "info": true,
        "dom": '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex items-center mb-4 md:mb-0"l><"flex items-center"f>>rt<"flex flex-col md:flex-row justify-between items-center mt-4"<"flex items-center mb-4 md:mb-0"i><"flex items-center"p>>',
        "language": {
            "search": "",
            "searchPlaceholder": "Search readings...",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ readings",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    // Handle new reading button click
    $('#new-reading-btn').on('click', function() {
        const customerId = $('#customer_select').val();
        const customerName = $('#customer_select option:selected').text();
        
        if (!customerId) {
            alert('Please select a customer first');
            return;
        }

        // Get the latest reading for this customer
        $.get(`/agent/customers/${customerId}/readings`, function(data) {
            let previousReading = 0;
            if (data && data.length > 0) {
                previousReading = data[0].current_reading; // Get the most recent reading
            }
            
            $('#modal_customer_id').val(customerId);
            $('#customer-name').text(customerName);
            $('#prev-reading').text(previousReading);
            $('#modal_previous_reading').val(previousReading);
            $('#msg').html('');
            $('#current_reading').val('');
            $('#units-used').text('-');
            
            $('#submitReadingModal').modal('show');
        });
    });

    // Handle customer selection
    $('#customer-select-form').on('submit', function(e) {
        e.preventDefault();
        const customerId = $('#customer_select').val();
        if (!customerId) {
            alert('Please select a customer');
            return;
        }

        // Load reading history for selected customer
        $.get(`/agent/customers/${customerId}/readings`, function(data) {
            readingsTable.clear();
            data.forEach(function(reading) {
                readingsTable.row.add([
                    new Date(reading.created_at).toLocaleDateString(),
                    reading.previous_reading,
                    reading.current_reading,
                    reading.units,
                    `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                        reading.status === 'approved' ? 'bg-green-900 text-green-300' :
                        reading.status === 'pending' ? 'bg-yellow-900 text-yellow-300' :
                        'bg-red-900 text-red-300'
                    }">${reading.status}</span>`,
                    `<button type="button" class="text-blue-400 hover:text-blue-300 submit-reading" 
                             data-id="${reading.customer_id}"
                             data-name="${reading.customer_name}"
                             data-prev="${reading.current_reading}">
                        New Reading
                    </button>`
                ]);
            });
            readingsTable.draw();
        });
    });

    // Handle form submission
    $('#reading-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitButton = $('#submit-button');
        
        // Validate form data
        const customerId = $('#modal_customer_id').val();
        const previousReading = parseFloat($('#modal_previous_reading').val());
        const currentReading = parseFloat($('#current_reading').val());

        if (!customerId) {
            $('#msg').html('<div class="text-red-400">Customer ID is required</div>');
            return;
        }

        if (isNaN(previousReading)) {
            $('#msg').html('<div class="text-red-400">Previous reading is required</div>');
            return;
        }

        if (isNaN(currentReading) || currentReading <= previousReading) {
            $('#msg').html('<div class="text-red-400">Current reading must be greater than previous reading</div>');
            return;
        }

        const formData = form.serialize();
        submitButton.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#msg').html('<div class="text-green-400">' + response.message + '</div>');
                setTimeout(() => {
                    $('#submitReadingModal').modal('hide');
                    $('#customer-select-form').trigger('submit'); // Refresh the table
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage += '<ul>' + Object.values(xhr.responseJSON.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>';
                }
                $('#msg').html('<div class="text-red-400">' + errorMessage + '</div>');
                submitButton.prop('disabled', false).text('Submit');
            }
        });
    });

    $('#current_reading').on('input', function() {
        const currentReading = parseFloat($(this).val()) || 0;
        const previousReading = parseFloat($('#modal_previous_reading').val()) || 0;
        const unitsUsed = Math.max(0, currentReading - previousReading);
        $('#units-used').text(unitsUsed.toFixed(2));
    });
});
</script>
@endsection 