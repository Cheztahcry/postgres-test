const radioButtons = document.querySelectorAll('input[name="acct-stat"]');
const stud_info = document.getElementById("acct-info-content");
const ld_info = document.getElementById("acct-tran-content");
const input_fields = stud_info ? stud_info.querySelectorAll('input') : [];
const edit_button = document.getElementById("edit");
const save_button = document.getElementById("save");
let button_stat = false;

function updateActiveTab() {
    radioButtons.forEach(radio => {
        const label = radio.closest('.status-option');
        if (!label) return;
        if (radio.checked) label.classList.add('active');
        else label.classList.remove('active');
    });
}

function setReadOnlyMode(enabled) {
    input_fields.forEach(input => {
        input.disabled = enabled;
        input.style.backgroundColor = enabled ? '#f4f5ff' : '#ffffff';
    });
}

function changeInfo() {
    const checkedRadio = document.querySelector('input[name="acct-stat"]:checked');
    updateActiveTab();

    if (checkedRadio && checkedRadio.value === "Student Information") {
        stud_info.style.display = "block";
        ld_info.style.display = "none";
        save_button.style.display = button_stat ? 'inline-flex' : 'none';
        setReadOnlyMode(!button_stat);
    } else if (checkedRadio && checkedRadio.value === "LD Information") {
        stud_info.style.display = "none";
        ld_info.style.display = "block";
        save_button.style.display = 'none';
        setReadOnlyMode(true);
        button_stat = false;
        if (edit_button) edit_button.style.backgroundColor = '#f4f5ff';
    }else if (checkedRadio && checkedRadio.value === "Bookmark") {
        stud_info.style.display = "none";
        ld_info.style.display = "none";
        save_button.style.display = 'none';
        setReadOnlyMode(true);
        button_stat = false;
        if (edit_button) edit_button.style.backgroundColor = '#f4f5ff';
    }else {
        stud_info.style.display = "none";
        ld_info.style.display = "none";
    }
}

if (edit_button) {
    edit_button.addEventListener("click", function() {
        button_stat = !button_stat;
        edit_button.style.backgroundColor = button_stat ? '#5060f2' : '#f4f5ff';
        edit_button.style.color = button_stat ? '#ffffff' : '#38416f';
        save_button.style.display = button_stat ? 'inline-flex' : 'none';
        setReadOnlyMode(!button_stat);
        if (password) password.disabled = !button_stat;
    });
}

// Save handler: submit updated fields via AJAX
if (save_button) {
    save_button.addEventListener('click', function() {
        // gather values
        const data = new FormData();
        input_fields.forEach(input => data.append(input.name, input.value));
        if (password) data.append('password', password.value);

        save_button.disabled = true;
        save_button.textContent = 'Saving...';

        fetch('update_owner.php', {
            method: 'POST',
            body: data
        }).then(r => r.json())
        .then(res => {
            if (res.success) {
                // disable editing mode
                button_stat = false;
                setReadOnlyMode(true);
                if (edit_button) {
                    edit_button.style.backgroundColor = '#f4f5ff';
                    edit_button.style.color = '#38416f';
                }
                save_button.style.display = 'none';
            } else {
                alert(res.message || 'Failed to save');
            }
        }).catch(err => {
            console.error(err);
            alert('An error occurred saving your information.');
        }).finally(() => {
            save_button.disabled = false;
            save_button.textContent = 'Save Information';
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    changeInfo(); 
});
radioButtons.forEach(radio => {
    radio.addEventListener('change', changeInfo);
});


