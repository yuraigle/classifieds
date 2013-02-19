$ ->
  $('[rel=tooltip]').tooltip()
  $('[rel=popover]').popover()

  $('.modal-link').click ->
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

  $('#question_type').live 'change', ->
    $('#question_predefined').parents('.control-group').toggle $(this).val() == 'select'

  # toggle category fields in advert form
  $('#advert_category').live 'change', ->
    $.post(
      '/classified/category/render'
      {id: $('#advert_category').val()}
      (data) ->
        $('#add_questions').html(data)
    )