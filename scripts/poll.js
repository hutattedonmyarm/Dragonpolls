window.addEventListener('DOMContentLoaded', () => {
  displayLocalTimestamps();
  for (const el of document.querySelectorAll('.option input[type=checkbox]')) {
    el.onclick = updateVotesRemaining;
  }
  document.querySelector('.success-banner').onclick = hideBanner;
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

function hideBanner() {
  //const banner = document.querySelector('.success-banner');
  const banner = document.querySelector('.banner-wrapper');
  const animationEnd = findTransitionEnd();
  banner.addEventListener(animationEnd, animationEnded);
  const rect = banner.getBoundingClientRect();
  console.log('Setting height to ', `${rect.height}px`);
  banner.style.height = `${rect.height}px`;
  banner.classList.add('hiding');
}

function animationEnded(event) {
  const banner = event.target;
  const isPhase1End = !banner.classList.contains('resizing');
  banner.innerText = '';
  if (isPhase1End) {
    console.log('Resizing');
    banner.classList.add('resizing');
    banner.style.height = '0';
  } else {
    event.target.removeEventListener(findTransitionEnd(), animationEnded);
    event.target.classList.add('hidden');
  }
}

function findTransitionEnd() {
  const e = document.createElement('div');
  const animations = {
      'transition': 'transitionend',
      'OTransition': 'oTransitionEnd',
      'MozTransition': 'transitionend',
      'WebkitTransition': 'webkitTransitionEnd'
  };
  for (const a in animations) {
      if (e.style[a] !== undefined) {
          return animations[a];
      }
  }
}