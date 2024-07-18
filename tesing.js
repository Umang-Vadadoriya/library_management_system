
function get_text1(event) {
    document.getElementById('user_unique_id_result').innerHTML = '';

    document.getElementById('user_id').value = event.child().textContent;
}
