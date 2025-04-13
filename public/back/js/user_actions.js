document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-user');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Récupération des données
            const userName = this.getAttribute('data-user-name');
            const deleteUrl = this.getAttribute('data-delete-url');
            const csrfToken = this.closest('form')?.querySelector('input[name="_token"]')?.value 
                           || document.querySelector('meta[name="csrf-token"]')?.content;

            // Configuration moderne de SweetAlert
            Swal.fire({
                title: 'Confirmation de suppression',
                html: `<div class="text-center">
                         <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                         <p>Voulez-vous vraiment supprimer l'utilisateur <b class="text-danger">${userName}</b> ?</p>
                         <p class="text-muted small">Cette action est irréversible.</p>
                       </div>`,
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger px-4 mx-2',
                    cancelButton: 'btn btn-secondary px-4 mx-2',
                    popup: 'border-radius-lg'
                },
                iconHtml: '<i class="fas fa-trash-alt"></i>',
                backdrop: `
                    rgba(0,0,0,0.7)
                    url("/images/trash-animation.gif")
                    center top
                    no-repeat
                `,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Création dynamique du formulaire
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    form.style.display = 'none';
                    
                    // Ajout des champs cachés
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = csrfToken;
                    form.appendChild(tokenInput);
                    
                    // Soumission du formulaire
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});