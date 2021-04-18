window.addEventListener('DOMContentLoaded', () => {
  const channelSelector = document.querySelector('select[name="channelid"]');
  document.querySelector('input[name="broadcast"]').disabled = channelSelector.value === '-1';
  channelSelector.onchange = validateBroadcastStatus;
});

function validateBroadcastStatus() {
  const postToChannel = parseInt(this.value) > 0;
  const checkbox = document.querySelector('input[name="broadcast"]');
  if (!postToChannel) {
    checkbox.dataset.wasChecked = checkbox.checked;
    checkbox.checked = false;
    checkbox.disabled = true;
  } else {
    if (checkbox.disabled && checkbox.dataset.wasChecked !== undefined) {
      checkbox.checked = checkbox.dataset.wasChecked === 'true';
    }
    checkbox.disabled = false;
  }
}