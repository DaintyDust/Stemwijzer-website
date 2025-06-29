const fileTypes = [
    "image/jpg",
    "image/jpeg",
    "image/png",
    "image/webp",
    "image/svg",
];

function validFileType(file) {
    return fileTypes.includes(file.type);
}


function updatePfp() {
    const fileInput = document.getElementById("profile_picture");

    fileInput.addEventListener("change", function (e) {
        console.log("File input changed:", fileInput.files[0]);
        e.preventDefault();

        const file = fileInput.files[0];

        if (!validFileType(file)) {
            alert("Ongeldig bestandstype. Alleen JPG, GIF en PNG zijn toegestaan.");
            return;
        }

        const formData = new FormData();
        formData.append("action", "pfp");
        formData.append("profile_picture", fileInput.files[0]);

        fetch("private/update-account.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // alert("Profielafbeelding bijgewerkt! " + data.message);
                    location.reload();
                } else {
                    alert("Er is een fout opgetreden bij het bijwerken van de profielafbeelding." + (data.message || "Onbekende fout"));
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Er is een fout opgetreden bij het bijwerken van de profielafbeelding.");
            });
    });

}


function setupPasswordEdit() {
    const passwordContainer = document.querySelector('.input-container input[name="password"]').parentElement;

    passwordContainer.addEventListener('click', function () {
        window.location.href = 'account.php?update=password';
    });
}


document.addEventListener("DOMContentLoaded", function () {
    updatePfp();
    setupPasswordEdit();
});