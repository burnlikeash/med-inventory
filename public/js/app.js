document.addEventListener('DOMContentLoaded', function () {
    const departmentSelect = document.getElementById('department_id');
    const locationSelect = document.getElementById('location_id');

    if (!departmentSelect || !locationSelect) {
        return;
    }

    function filterLocations() {
        const selectedDepartment = departmentSelect.value;

        Array.from(locationSelect.options).forEach(function (option) {
            if (option.value === '') {
                option.hidden = false;
                return;
            }

            option.hidden = selectedDepartment !== ''
                && option.dataset.department !== selectedDepartment;
        });

        const selectedOption = locationSelect.options[locationSelect.selectedIndex];

        if (
            selectedOption
            && selectedOption.value !== ''
            && selectedDepartment !== ''
            && selectedOption.dataset.department !== selectedDepartment
        ) {
            locationSelect.value = '';
        }
    }

    departmentSelect.addEventListener('change', filterLocations);
    filterLocations();
});
