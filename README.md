# cot-pagelist
Rendering page widgets anywhere

## Использование:

```html
{PHP|pagelist($tpl, $items, $order, $condition, $cat, $blacklist, $whitelist, $sub, $pagination, $noself, $offset)}
```

Назначение параметров (в скобках значение по умолчанию -- если не указано пользователем):
* $tpl указывает на имя шаблона (pagelist)
* $items указывает на количество выводимых записей (0 -- вывести все)
* $order указывает на сортировку в формате MYSQL, например `page_date ASC` (по умолчанию без сортировки)
* $condition указывает на условие в формате MYSQL, например, `page_ownerid = 1` (по умолчанию без условия)
* $cat указывает на отдельную категорию страниц (по умолчанию без категории)
* $blacklist указывает на черный список категорий страниц, т.е. брать страницы из всех разделов, кроме указанных (по умолчанию без черного списка)
* $whitelist указывает на белый список категорий страниц, т.е. брать страницы только из указанных разделов (по умолчанию без белого списка)
* $sub указывает на необходимость вывода страниц из подразделов (по умолчанию true)
* $pagination указывает на имя переменной вывода постраничного списка (по умолчанию pld)
* $noself указывает на необходимость включать в вывод страницу, из которой осуществляется запрос (по умолчанию false)
* $offset указывает на необходимость сдвига, т.е. исключения определенного количества предстоящих страниц -- например, для вывода всех, кроме первой страницы (по умолчанию сдвиг отсутствует)

## История:

вер. 2.02 -- исправление бага, связанного с генерацией user tags

вер. 2.01 (Изменения по сравнению с первым релизом от Trustmaster):

1. Удалил опцию вывода количества комментариев -- проще делать это по месту при помощи конструкций вида {PAGE_ROW_ID|cot_comments_count('page', $this)|cot_declension($this, 'Comments')}
2. Опционизировал поддержку user tags (в некоторых проектах это не требовалось вообще)
3. Добавил поддержку i18n (в некоторых проектах требовалось)
4. Подчистил немного
5. Добавил поддержку Star Ratings (опционально, на всякий случай)
6. Добавил параметр `$offset`

===

## How to Use:

## Version History:

ver. 2.02 -- bug fix related to the user tags generation

ver. 2.01 (Changes as compared to the initial release by Trustmaster):

1. Removed comments parts -- can be replaced using {PAGE_ROW_ID|cot_comments_count('page', $this)|cot_declension($this, 'Comments')} as needed
2. Optional user tags generation (did not need this in some projests)
3. Added i18n support
4. Minor cleanup
5. Added Star Ratings plugin support (optional)
6. Added `$offset` parameter