let lan = document.getElementsByClassName("language");
Array.from(lan).forEach(element => (element.addEventListener("click", changeLocale)));

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
