
const DATA_KEY = 'lte.cardwidget'
const EVENT_KEY = `.${DATA_KEY}`

const EVENT_EXPANDED = `expanded${EVENT_KEY}`
const EVENT_COLLAPSED = `collapsed${EVENT_KEY}`
const childrens = `.card-body, .card-body1, .card-footer`


function collapse(element) {
    console.log("function collapse(element)")
    var d = element.parents(".card").first()
    d.addClass('collapsing-card').children(childrens)
        .slideUp("normal", () => {
            d.addClass('collapsed-card').removeClass('collapsing-card')
        })

    d.find(`> .card-header [data-card-widget="collapse"] .fa-minus`)
        .addClass('fa-plus')
        .removeClass('fa-minus')

    element.trigger($.Event(EVENT_COLLAPSED), d)
}

function expand(element) {
    console.log("function expand(element)")

    var d = element.parents(".card").first()
    d.addClass('expanding-card').children(childrens)
        .slideDown("normal", () => {
            d.removeClass('collapsed-card').removeClass('expanding-card')
        })

    d.find(`> .card-header [data-card-widget="collapse"] .fa-plus`)
        .addClass('fa-minus')
        .removeClass('fa-plus')

    element.trigger($.Event(EVENT_EXPANDED), d)
}


$(document).on('click', '[data-card-widget="collapse"]', function (event) {
    if (event) {
        event.preventDefault()
    }
    var element = $(this);
    var d = element.parents(".card").first()
    if (d.hasClass('collapsed-card')) {
        expand(element)
        return
    }

    collapse(element)

})
