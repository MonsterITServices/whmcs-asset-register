// Add any module-specific admin javascript here.
function toggleIpAddressField(selectedValue) {
    var staticIpContainer = document.getElementById('static-ip-container');
    if (selectedValue === 'static') {
        staticIpContainer.style.display = 'block';
    } else {
        staticIpContainer.style.display = 'none';
    }
}