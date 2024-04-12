(function () {
    'use strict';

    var forms = document.querySelectorAll('.needs-validation');

    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var inputs = form.querySelectorAll('.form-control');
                var isValid = true;
                inputs.forEach(function(input, index) {
                    if (!input.checkValidity()) {
                        isValid = false;
                        var feedback = form.querySelectorAll('.invalid-feedback')[index];
                        feedback.style.display = 'block';
                    } else {
                        var feedback = form.querySelectorAll('.invalid-feedback')[index];
                        feedback.style.display = 'none'; // Hide invalid feedback if input is valid
                    }
                });
                
                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);

            var inputFields = form.querySelectorAll('.form-control');
            inputFields.forEach(function(input, index) {
                input.addEventListener('input', function() {
                    var isValidInput = input.checkValidity();
                    var feedback = form.querySelectorAll('.valid-feedback')[index];
                    var invalidFeedback = form.querySelectorAll('.invalid-feedback')[index];
                    if (isValidInput) {
                        feedback.style.display = 'none';
                        invalidFeedback.style.display = 'none'; // Hide invalid feedback if input is valid
                    }
                });
            });
        });
})();
