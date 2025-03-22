/**
 * Changer de moteur de recherche (en mode invité)
 * @param {*} searchEngineId 
 */
async function setSearchEngine(searchEngineId) {
    console.log(searchEngineId);

    const response = await fetch(`/fetch/search.php?id=${encodeURIComponent(searchEngineId)}`, {
        method: 'GET'
    });

    if (response.ok) {
        const searchEngine = await response.json();
        const form = document.querySelector("form#search-bar");
        const img = document.querySelector("form#search-bar img");
        const input = document.querySelector("form#search-bar input");

        form.setAttribute("action", searchEngine.result_url);
        img.setAttribute("src", searchEngine.icon);
        input.setAttribute("name", searchEngine.query_param);
        input.setAttribute("placeholder", `Rechercher avec ${searchEngine.name}`);

        document.cookie = `start-search-engine-id=${searchEngineId};path=/;max-age=31536000`;
    } else {
        alert("Désolé, une erreur est survenue.");
    }
}

document.addEventListener("DOMContentLoaded", function (e) {
    document.querySelectorAll('button[data-trigger="search-engine-change"]').forEach(function (btn) {
        btn.addEventListener("click", () => setSearchEngine(btn.getAttribute("data-search-id")));
    });
});