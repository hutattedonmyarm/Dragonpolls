window.addEventListener('DOMContentLoaded', (event) => {
  displayLocalTimestamps();
});

function displayLocalTimestamps() {
  for (const el of document.getElementsByTagName('time')) {
    let dateObj = new Date(el.getAttribute('datetime'));
    el.innerText = dateObj.toLocaleString();
  }
}