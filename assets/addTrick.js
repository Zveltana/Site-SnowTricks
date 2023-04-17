const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
    let index = collectionHolder.dataset.index;
    const item = document.createElement("div");
    item.classList.add("mt-5");
    item.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, index);

    item.querySelector('.btn-remove').addEventListener("click", () => item.remove());

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};

const removeItem = (e) => {
    e.currentTarget.closest(".mt-5").remove()
}

document.querySelectorAll('.btn-remove').forEach(btn => btn.addEventListener("click", removeItem))

document.querySelectorAll('.btn-new').forEach(btn => btn.addEventListener("click", newItem))