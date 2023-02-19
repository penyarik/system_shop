let lan = document.getElementsByClassName("language");
let currency = document.getElementsByClassName("currency");
console.log(currency);
Array.from(lan).forEach(element => (element.addEventListener("click", changeLocale)));
Array.from(currency).forEach(element => (element.addEventListener("click", changeCurrency)));

function changeLocale()
{
    const Http = new XMLHttpRequest();
    const url = this.getAttribute('data-route');
    Http.open("POST", url);
    Http.send();

    Http.onreadystatechange = (e) => {
        if (Http.status === 201) {
            location.reload()
        }
    }
}

function changeCurrency()
{
    console.log(223323);
    const Http = new XMLHttpRequest();
    const url = this.getAttribute('data-route');
    Http.open("POST", url);
    Http.send();

    Http.onreadystatechange = (e) => {
        if (Http.status === 201) {
            location.reload()
        }
    }
}
