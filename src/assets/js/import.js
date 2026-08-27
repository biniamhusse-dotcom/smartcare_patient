document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('importBtn');
    const bar = document.getElementById('progressBar');
    const msg = document.getElementById('statusMessage');
    const file = document.getElementById('csv_file').files[0];

    if (!file) return alert("Select a file");

    const formData = new FormData();
    formData.append('csv_file', file);

    document.getElementById('progressSection').classList.remove('d-none');
    btn.disabled = true;

    const xhr = new XMLHttpRequest();
    
    // Update Percentage
    xhr.upload.onprogress = (e) => {
        let p = Math.round((e.loaded / e.total) * 100);
        bar.style.width = p + '%';
        bar.innerText = p + '%';
        if(p === 100) msg.innerText = "Saving to Database... Please wait.";
    };

    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            const response = xhr.responseText.trim();
            if (response === "success") {
                msg.innerHTML = "<b class='text-success'>COMPLETED SUCCESSFULLY!</b>";
                setTimeout(() => location.reload(), 1500);
            } else {
                msg.innerHTML = "<b class='text-danger'>FAILED: " + response + "</b>";
                btn.disabled = false;
            }
        }
    };

    xhr.open('POST', 'import.php', true);
    xhr.send(formData);
});