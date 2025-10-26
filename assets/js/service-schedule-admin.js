(function ($) {
  'use strict';

  const wrapperSelector = '.hram-service-schedule-services';

  const templates = {
    getRow: (index, values = {}) => {
      const time = values.time || '';
      const title = values.title || '';

      return `
        <div class="hram-service-schedule-services__row" data-index="${index}">
          <div class="hram-service-schedule-services__field">
            <label>
              <span>${window.hramServiceScheduleAdmin?.i18n?.time || 'Время'}</span>
              <input type="time" name="service_schedule_services[${index}][time]" value="${time}" step="60" />
            </label>
          </div>
          <div class="hram-service-schedule-services__field hram-service-schedule-services__field--title">
            <label>
              <span>${window.hramServiceScheduleAdmin?.i18n?.title || 'Название службы'}</span>
              <input type="text" name="service_schedule_services[${index}][title]" value="${title}" />
            </label>
          </div>
          <button type="button" class="button-link-delete hram-service-schedule-services__remove" aria-label="${window.hramServiceScheduleAdmin?.i18n?.remove || 'Удалить'}">
            &times;
          </button>
        </div>
      `;
    },
  };

  const refreshIndexes = ($rows) => {
    $rows.each(function (index) {
      const $row = $(this);
      $row.attr('data-index', index);
      $row.find('input').each(function () {
        const $input = $(this);
        const name = $input.attr('name') || '';
        const newName = name.replace(/service_schedule_services\[[0-9]+\]/, `service_schedule_services[${index}]`);
        $input.attr('name', newName);
      });
    });
  };

  const addRow = ($container, values = {}) => {
    const index = $container.children('.hram-service-schedule-services__row').length;
    const rowMarkup = templates.getRow(index, values);
    $container.append(rowMarkup);
  };

  const init = () => {
    const $wrapper = $(wrapperSelector);
    if (!$wrapper.length) {
      return;
    }

    $wrapper.each(function () {
      const $container = $(this);
      const $button = $container.siblings('.hram-service-schedule-services__actions').find('.hram-service-schedule-services__add');

      $button.on('click', (event) => {
        event.preventDefault();
        addRow($container);
      });

      $container.on('click', '.hram-service-schedule-services__remove', function (event) {
        event.preventDefault();
        $(this).closest('.hram-service-schedule-services__row').remove();
        refreshIndexes($container.children('.hram-service-schedule-services__row'));
      });

      // Ensure at least one row for better UX.
      if (!$container.children().length) {
        addRow($container);
      }
    });
  };

  $(init);
})(jQuery);
