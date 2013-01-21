$ ->
    $('[rel=tooltip]').tooltip()
    $('[rel=popover]').popover()

    $('[rel=modal]').click ->
        resource = $(this).attr('data-resource')
        $.post(
            resource.replace(/_/g, "/")
            {id: $(this).attr('data-id')}
            (data) ->
                $('#modal').html(data)
                $('#'+resource).modal('show')
                form = $('#'+resource+'_form')
                submit = $('#'+resource+'_submit')
                messages = $('#'+resource+'_messages')
                submit.click -> form.submit()
                form.submit ->
                    $.post(
                        form.attr('action')
                        form.serialize()
                        (data) ->
                            messages.html(data.messages)
                            if data.status == "OK"
                                $('#'+resource).modal('hide')
                                window.location.reload()
                        "json"
                    )
                    return false
        )
