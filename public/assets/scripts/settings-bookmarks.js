async function addBookmark() {
    const nameInput = document.querySelector("#bookmarks tfoot input[type='text']");
    const urlInput = document.querySelector("#bookmarks tfoot input[type='url']");
    const submitBtn = document.querySelector("#bookmarks tfoot button[type='button']");

    if (!nameInput.value || !urlInput.value) {
        alert("Veuillez saisir un nom et une URL.");
        return;
    }

    nameInput.setAttribute("disabled", "disabled");
    urlInput.setAttribute("disabled", "disabled");
    submitBtn.setAttribute("disabled", "disabled");

    const response = await fetch("/fetch/bookmark.php", {
        method: 'POST',
        body: new URLSearchParams({ 'name': nameInput.value, 'url': urlInput.value })
    });

    if (response.ok) {
        const bookmark = await response.json();

        document.querySelector("#bookmarks tbody").innerHTML = document.querySelector("#bookmarks tbody").innerHTML.concat(`
            <tr id="bookmark-${bookmark.uuid}">
                <td><img src="${bookmark.icon}" alt="" height="16" width="16"></td>
                <td>${bookmark.name}</td>
                <td>${bookmark.url}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteBookmark('${bookmark.uuid}')"><i class="bi bi-trash"></i></button></td>
            </tr>
        `);

        nameInput.value = '';
        urlInput.value = '';
    } else {
        alert("Une erreur est survenue.");
    }

    nameInput.removeAttribute("disabled");
    urlInput.removeAttribute("disabled");
    submitBtn.removeAttribute("disabled");
}

async function deleteBookmark(uuid) {
    const response = await fetch(`/fetch/bookmark.php?uuid=${encodeURIComponent(uuid)}`, {
        method: 'DELETE'
    });

    if (response.ok) {
        document.querySelector(`#bookmark-${uuid}`).remove();
    } else {
        alert("Une erreur est survenue.");
    }
}