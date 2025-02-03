document.addEventListener("DOMContentLoaded", function () {
    const switchLogin = document.getElementById("switch-login");
    const formTitle = document.getElementById("form-title");
    const authForm = document.getElementById("auth-form");
    const btnSubmit = authForm.querySelector(".btn");
    const actionInput = authForm.querySelector("input[name='action']");
    
    const nomGroup = document.getElementById("nom-group");
    const prenomGroup = document.getElementById("prenom-group");
    const nomInput = document.getElementById("nom");
    const prenomInput = document.getElementById("prenom");
    
    let isLogin = false; // Suivi de l'état du formulaire

    function toggleForm() {
        isLogin = !isLogin; // Inverse l'état

        if (isLogin) {
            formTitle.innerText = "Connexion";
            nomGroup.style.visibility = "hidden";
            prenomGroup.style.visibility = "hidden";
            nomGroup.style.opacity = "0";
            prenomGroup.style.opacity = "0";
            nomGroup.style.height = "0";
            prenomGroup.style.height = "0";
            nomInput.disabled = true;
            prenomInput.disabled = true;
            btnSubmit.innerText = "Se connecter";
            actionInput.value = "connexion";
            switchLogin.innerText = "Pas encore de compte ?";
        } else {
            formTitle.innerText = "Inscription";
            nomGroup.style.visibility = "visible";
            prenomGroup.style.visibility = "visible";
            nomGroup.style.opacity = "1";
            prenomGroup.style.opacity = "1";
            nomGroup.style.height = "auto";
            prenomGroup.style.height = "auto";
            nomInput.disabled = false;
            prenomInput.disabled = false;
            btnSubmit.innerText = "S'inscrire";
            actionInput.value = "inscription";
            switchLogin.innerText = "Déjà un compte ?";
        }
    }

    switchLogin.addEventListener("click", function (event) {
        event.preventDefault();
        toggleForm();
    });
});
