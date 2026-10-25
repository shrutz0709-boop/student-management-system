document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#studentForm');
  if (form) {
    form.addEventListener('submit', (event) => {
      const name = document.querySelector('#name');
      const roll = document.querySelector('#roll_number');
      const email = document.querySelector('#email');
      const course = document.querySelector('#course');
      const semester = document.querySelector('#semester');
      const phone = document.querySelector('#phone');
      const requiredFields = [name, roll, email, course, semester];
      const emptyField = requiredFields.find((field) => !field.value.trim());
      if (emptyField) {
        event.preventDefault();
        emptyField.focus();
        emptyField.setCustomValidity('Please complete this required field.');
        emptyField.reportValidity();
        emptyField.setCustomValidity('');
        return;
      }
      if (!email.checkValidity()) {
        event.preventDefault();
        email.focus();
        email.reportValidity();
        return;
      }
      if (phone.value && !/^[0-9+()\- ]{7,20}$/.test(phone.value.trim())) {
        event.preventDefault();
        phone.setCustomValidity('Enter a valid phone number (7–20 characters).');
        phone.reportValidity();
        phone.setCustomValidity('');
      }
    });
  }

  document.querySelectorAll('.delete-form').forEach((deleteForm) => {
    deleteForm.addEventListener('submit', (event) => {
      const studentName = deleteForm.dataset.studentName || 'this student';
      if (!window.confirm(`Delete ${studentName}'s record? This action cannot be undone.`)) {
        event.preventDefault();
      }
    });
  });
});
