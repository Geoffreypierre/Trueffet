const a_genre = document.getElementById('genre')
const arrow = document.getElementById('arrow-down')
const catList = document.getElementById('genre-cats')
const basket = document.getElementById('basketCountDiv')

a_genre.addEventListener('mouseover', () => {
    arrow.style.rotate = '0deg';
    arrow.style.transition = '0.5s';
})

a_genre.addEventListener('mouseleave', () => {
    arrow.style.rotate = '-90deg';
})

catList.addEventListener('mouseover', () => {
    arrow.style.rotate = '0deg';
    arrow.style.transition = '0.5s';
})

catList.addEventListener('mouseleave', () => {
    arrow.style.rotate = '-90deg';
})

async function getNbProduct() {
    const response = await fetch('http://127.0.0.1:8000/basket/api', {method: 'GET'})
    if (response.status === 200) {
        const json = await response.json()
        for (let key in json) {
            if (json[key] !== 0) {
                basket.style.display = 'flex'
                const element = document.getElementById(key) //Element HTML
                element.innerText = json[key];
            }
        }
    }
}

getNbProduct()





