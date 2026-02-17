import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';

// Try these in order, adjust to the one that resolves in your install
let EditEventModal;
try { EditEventModal = require('eddiewebb/flarum-calendar/components/EditEventModal').default; } catch {}
if (!EditEventModal) { try { EditEventModal = require('webbinaro/flarum-calendar/components/EditEventModal').default; } catch {} }
if (!EditEventModal) { try { EditEventModal = require('webbinaro/advcalendar/components/EditEventModal').default; } catch {} }

app.initializers.add('keith-extend-calendar', () => {
  if (!EditEventModal) {
    // Fallback: silently do nothing if the modal path cannot be resolved
    // You can inspect the calendar’s forum bundle to locate the correct module id
    return;
  }

  // 1) Render a Website input field in the form
  extend(EditEventModal.prototype, 'fields', function (items) {
    // Grab current value from loaded event attributes if present
    const current = this.attrs?.event?.attributes?.website || '';
    if (!this.website) this.website = m.stream(current);

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
      50
    );
  });

  // 2) Include website in the request payload on save
  extend(EditEventModal.prototype, 'data', function (data) {
    data.attributes = data.attributes || {};
    data.attributes.website = (this.website && this.website()) || null;
  });
});