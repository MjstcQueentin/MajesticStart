async function fetchWeather() {
    const cityIndicator = document.querySelectorAll("#weather .text-muted").item(0);
    const weatherBlocks = document.querySelectorAll("#weather .weather-container").item(0);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async function (position) {
                const response = await fetch(`/fetch/weather.php?lat=${position.coords.latitude}&lon=${position.coords.longitude}`);

                if (!response.ok) {
                    weatherBlocks.innerHTML = `<div class="alert alert-danger mx-5">
                        <i class="bi bi-bug"></i> Une erreur est survenue de notre côté. Merci de réessayer dans un moment.
                    </div>`;
                } else {
                    const weather = await response.json();

                    cityIndicator.innerHTML = cityIndicator.innerHTML.replace("Ville", `${weather.city}, ${weather.country}`);
                    cityIndicator.classList.remove("d-none");
                    weatherBlocks.innerHTML = weather.forecast.map((value, index, array) => {
                        return `<div class="weather-block" style="background-image: url('${value.background}');">
                            <h5 class="mb-2">${value.day}</h5>
                            <p class="mb-0">${value.temp} °C</p>
                            <p class="mb-0">${value.weather}</p>
                        </div>`;
                    }).join("\n");
                }
            },
            function (error) {
                switch (error.code) {
                    case 1:
                        weatherBlocks.innerHTML = `<div class="alert alert-info mx-5">
                            <i class="bi bi-info-circle"></i> Nous ne pouvons pas afficher la météo car vous avez refusé la géolocalisation.
                        </div>`;
                        break;
                    case 2:
                    case 3:
                        weatherBlocks.innerHTML = `<div class="alert alert-warning mx-5">
                            <i class="bi bi-exclamation-triangle"></i> Nous ne pouvons pas afficher la météo car la géolocalisation a échoué.
                        </div>`;
                        break;
                }
            }
        );
    } else {

    }
}

document.addEventListener('DOMContentLoaded', fetchWeather);