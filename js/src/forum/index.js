import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';

app.initializers.add('keith-extend-calendar', () => {
  console.log('[extend-calendar] Frontend active.');

  //
  // 1. Locate the calendar modal dynamically.
  //
  // The calendar extension does NOT export EditEventModal as an importable module.
  // Its modules are bundled into Flarum's app.core.compat registry.
  //
  let EditEventModal = null;

  for (const key in app.core.compat) {
    const mod = app.core.compat[key];
    if (!mod || typeof mod !== 'object') continue;

    const candidate = mod.default || mod;

    // Detect the modal by its className property
    if (
      candidate &&
      candidate.prototype &&
      candidate.prototype.className === 'EditEventModal'
    ) {
      EditEventModal = candidate;
      console.log('[extend-calendar] Found EditEventModal via compat:', key);
      break;
    }
  }

  //
  // 2. Exit if the modal can't be found
  //
  if (!EditEventModal) {
    console.warn('[extend-calendar] EditEventModal not found. Website field NOT injected.');
    return;
  }

  //
  // 3. Inject the Website input into the modal fields
  //
  extend(EditEventModal.prototype, 'fields', function (items) {
    // Initial value from event attributes
    const current = this.attrs?.event?.attributes?.website || '';

    // Only create the stream once
    if (!this.website) {
      this.website = m.stream(current);
    }

    items.add(
      'website',
      m('.Form-group', [
        m('label', 'Website'),
        m('input.FormControl', {
          type: 'url',
          placeholder: 'https://example.com',
          value: this.website(),
          oninput: (e) => this.website(e.target.value)
        })
      ]),
      50 // order weight
    );
  });

  //
  // 4. Add the website field to the JSON:API payload on save
  //
  extend(EditEventModal.prototype, 'data', function (data) {
    data.attributes = data.attributes || {};
    data.attributes.website = this.website ? this.website() : null;
  });
});