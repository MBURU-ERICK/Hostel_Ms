<!-- Reject Hostel Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Reject Hostel</h3>
            <div class="mt-2 px-7 py-3">
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_approved" value="0">
                    <input type="hidden" name="is_available" value="0">
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 text-left mb-2">
                            Reason for Rejection
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  rows="3" placeholder="Please provide a reason for rejecting this hostel..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                            Reject Hostel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Contact Landlord Modal -->
<div id="contactModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                <i class="fas fa-envelope text-indigo-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Contact Landlord</h3>
            <div class="mt-2 px-7 py-3">
                <div id="landlordInfo" class="text-left mb-4">
                    <!-- Landlord info will be loaded here -->
                </div>
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="closeContactModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Close
                    </button>
                    <a id="contactEmailLink"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Email
                    </a>
                    <a id="contactPhoneLink"
                       class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        Call
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Contact Landlord Function
function contactLandlord(landlordId) {
    // In a real application, you would fetch landlord details via AJAX
    // For now, we'll use the data we have from the page
    const landlord = {
        name: "{{ $hostel->user->name ?? 'Landlord' }}",
        email: "{{ $hostel->user->email ?? '' }}",
        phone: "{{ $hostel->user->phone ?? '' }}"
    };

    // Update modal content
    document.getElementById('landlordInfo').innerHTML = `
        <div class="space-y-2">
            <p><strong>Name:</strong> ${landlord.name}</p>
            <p><strong>Email:</strong> ${landlord.email || 'Not provided'}</p>
            <p><strong>Phone:</strong> ${landlord.phone || 'Not provided'}</p>
        </div>
    `;

    // Update contact links
    const emailLink = document.getElementById('contactEmailLink');
    const phoneLink = document.getElementById('contactPhoneLink');

    if (landlord.email) {
        emailLink.href = `mailto:${landlord.email}?subject=Regarding Hostel: {{ $hostel->name }}`;
        emailLink.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        emailLink.href = '#';
        emailLink.classList.add('opacity-50', 'cursor-not-allowed');
        emailLink.onclick = (e) => e.preventDefault();
    }

    if (landlord.phone) {
        phoneLink.href = `tel:${landlord.phone}`;
        phoneLink.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        phoneLink.href = '#';
        phoneLink.classList.add('opacity-50', 'cursor-not-allowed');
        phoneLink.onclick = (e) => e.preventDefault();
    }

    // Show modal
    document.getElementById('contactModal').classList.remove('hidden');
}

// Reject Hostel Modal Functions
// Reject Hostel Modal Functions - Dynamic version
function showRejectModal(hostelId) {
    // Create the route dynamically using the current URL structure
    const baseUrl = window.location.origin;
    const actionUrl = `${baseUrl}/admin/hostels/${hostelId}`;

    document.getElementById('rejectForm').action = actionUrl;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const rejectModal = document.getElementById('rejectModal');
    const contactModal = document.getElementById('contactModal');

    if (event.target === rejectModal) {
        closeRejectModal();
    }
    if (event.target === contactModal) {
        closeContactModal();
    }
});

// Handle form submissions with fetch for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Handle reject form submission
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeRejectModal();
                    showNotification('Hostel rejected successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error rejecting hostel: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while rejecting the hostel.', 'error');
            });
        });
    }

    // Handle edit form submission
    const editForm = document.getElementById('editHostelForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Hostel updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Error updating hostel: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the hostel.', 'error');
            });
        });
    }
});

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Animate in
    requestAnimationFrame(() => {
        notification.classList.remove('translate-x-full');
    });

    // Remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}
</script>
