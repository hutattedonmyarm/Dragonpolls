window.addEventListener('DOMContentLoaded', () => {
  displayLocalTimestamps();
  for (const el of document.querySelectorAll('.option input[type=checkbox]')) {
    el.onclick = updateVotesRemaining;
  }
});

function displayLocalTimestamps() {
  for (const el of document.getElementsByTagName('time')) {
    const dateObj = new Date(el.getAttribute('datetime'));
    const relDate = compareDateToToday(dateObj);
    el.innerText = relDate === 0
      ? `Today, ${dateObj.toLocaleTimeString()}`
      : dateObj.toLocaleDateString();
  }
}

function compareDateToToday(date) {
  const today = new Date();
  if (today.getDate() === date.getDate()
    && today.getMonth() === date.getMonth()
    && date.getFullYear() === today.getFullYear()) {
    return 0;
  }
  today.setHours(0, 0, 0, 0);
  return today > date ? -1 : 1;
}

function updateVotesRemaining() {
  const numChecked = document.querySelectorAll('.option input[type=checkbox]:checked').length;
  const votesRemainingElement = document.querySelector('.votes-remaining');
  const total = votesRemainingElement.dataset.maxVotes;
  const remaining = Math.max(0, total - numChecked);
  const plural = remaining === 1 ? '' : 's';
  votesRemainingElement.innerText = `${remaining} Vote${plural} remaining`;
  const voteButton = document.querySelector('button[name=submit_vote]');
  const canVoteInitial = voteButton.dataset.canVote === 'true';
  voteButton.disabled = !canVoteInitial || (total - numChecked) < 0 || numChecked === 0;
}