window.addEventListener('DOMContentLoaded', () => {
  for (const el of document.querySelectorAll('.create-poll input')) {
    el.onchange = validatePoll;
  }
});

function validatePoll() {
  let errors = [];
  const form = document.querySelector('.create-poll');
  if (!form.querySelector('input[name=prompt]').value) {
    errors.push('Prompt cannot be empty');
  }
  const numOptionsProvided = Array.from(form.querySelectorAll('input[name="option[]"]'))
  .map(x => !!x.value)
  .filter(x => x)
  .length;
  if (numOptionsProvided <= 1) {
    errors.push('At least two options must be provided');
  }

  const maxOptions = parseInt(form.querySelector('input[name=max_options]').value);
  if (numOptionsProvided > 1 && (isNaN(maxOptions) || maxOptions <= 0 || maxOptions > numOptionsProvided)) {
    errors.push(`Max Options needs to at least 1 and not more than ${numOptionsProvided}`);
  }

  const durationDays = parseInt(form.querySelector('input[name=duration_days]').value);
  const durationHours = parseInt(form.querySelector('input[name=duration_hours]').value);
  const durationMinutes = parseInt(form.querySelector('input[name=duration_minutes]').value);
  const durationTotalMinutes = durationDays*60*24 + durationHours * 60 + durationMinutes;
  // 20160 is the max duration accepted by pnut
  if (isNaN(durationTotalMinutes) || durationTotalMinutes < 1 || durationTotalMinutes > 20160) {
    errors.push('Duration must be more than 1 and less than 20160 minutes');
  }

  form.querySelector('input[name=duration_days] ~ span').innerText = durationDays === 1 ? 'day' : 'days';
  form.querySelector('input[name=duration_hours] ~ span').innerText = durationHours === 1 ? 'hour' : 'hours';
  form.querySelector('input[name=duration_minutes] ~ span').innerText = durationMinutes === 1 ? 'minute' : 'minutes';

  const closesAtLabel = document.getElementById('openUntil');
  if (!isNaN(durationTotalMinutes)) {
    // Add duration_total_minutes to the curent Date
    const closes_at = new Date(new Date().getTime() + durationTotalMinutes*60000);
    closesAtLabel.innerText = `Open until ${closes_at.toLocaleString()}`;
  } else {
    closesAtLabel.innerText = '';
  }

  const errorSpan = form.querySelector('.error');
  if (errors.length) {
    errorSpan.innerHTML = `<ul><li>${errors.join('</li><li>')}</li></ul>`;
  } else {
    errorSpan.innerText = '';
  }
  form.querySelector('button[name=submit]').disabled = !!errors.length;

}