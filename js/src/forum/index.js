import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';

app.initializers.add('keith-extend-calendar', () => {
  //
  // Attempt to locate the calendar extension’s modal constructor
  // by scanning loaded modules at runtime.
  //
  // This pattern is used when an extension does NOT export modules directly.
  //
  let EditEventModal = null;

  // Flarum packs modules in app.core.compat namespace
  for (const key in app.core.compat) {
    const mod = app.core.compat[key];
    if (mod && mod.default && mod.default.prototype && mod.default.prototype.className === 'EditEventModal') {
      EditEventModal = mod.default;
      break;
    }
  }

  if (!EditEventModal) {
    console.warn('[extend-calendar] Could not locate EditEventModal');
    return;
  }

  // Inject the Website field into the modal
  extend(EditEventModal.prototype, 'fields', function (items) {
    const current = this.attrs?.event?.attributes?.website || '';
    if (!this.website) this.website = m.stream(current);

    items.add('website',
      m('.Form-group', [
        m('label', 'Website'),
        m('input.FormControl', {
          type: 'url',
          placeholder: 'https://example.com',
          value: this.website(),
          oninput: (e) => this.website(e.target.value)
        })
      ]),
      50
    );
  });

  // Add website to payload
  extend(EditEventModal.prototype, 'data', function (data) {
    data.attributes ??= {};
    data.attributes.website = this.website ? this.website() : null;
  });
});