document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('open');
            const icon = this.querySelector('i');
            if (mainNav.classList.contains('open')) {
                icon.classList.replace('fa-bars', 'fa-times');
            } else {
                icon.classList.replace('fa-times', 'fa-bars');
            }
        });
    }
    
    // Auto-hide flash messages
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.transition = 'all 0.5s ease';
            flashMessage.style.transform = 'translateX(120%)';
            flashMessage.style.opacity = '0';
            setTimeout(() => flashMessage.remove(), 500);
        }, 4000);
    }
    
    // Quantity controls
    document.querySelectorAll('.qty-control').forEach(control => {
        const input = control.querySelector('input');
        const minus = control.querySelector('.qty-minus');
        const plus = control.querySelector('.qty-plus');
        
        if (minus && input) {
            minus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    input.value = val - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
        
        if (plus && input) {
            plus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                input.value = val + 1;
                input.dispatchEvent(new Event('change'));
            });
        }
    });
    
    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });
    
    // Image preview
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});