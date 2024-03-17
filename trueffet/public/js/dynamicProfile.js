let edit_button = document.getElementById('btn-edit')
let btn = document.querySelector('.btn-edit-profile')
let div_photo_profile = document.querySelector('.fpp')
let form = document.querySelectorAll('.info-user-form input')

for (let input of form) {
    input.disabled = true
}

edit_button.addEventListener('click', () => {
        div_photo_profile.classList.remove('hidden')
        edit_button.style.display = 'none'
        btn.style.display = 'flex'
        for (let input of form) {
            input.style.border = 'solid 2px black'
            input.style.borderRadius = '8px'
            input.disabled = false
        }
    }
)



