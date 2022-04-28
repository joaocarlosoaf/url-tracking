<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="form-group">
                        <form>
                          @csrf
                          <label for="url">URL do Website:</label>
                          <input type="text" class="form-control" id="url" name="url">
                          <button id="add_url_id" type="button" class="btn btn-primary mt-3">Adicionar</button>
                        </form>
                    </div>

            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                  <h3>Websites status:</h3>

                  <table class="table mt-5">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">URL</th>
                          <th scope="col">Ultima verificação</th>
                          <th scope="col">Status</th>
                          <th scope="col">Conteúdo</th>
                          <th scope="col">Ações</th>
                        </tr>
                      </thead>
                      <tbody id=tb-url-tracking></tbody>
                  </table>
            </div>
        </div>
    </div>

  <script>

    $(document).ready(function() {
      
      getListUrlsTracking();
    
      $('#add_url_id').on("click", function(){
        $('#add_url_id').attr('disabled', true);
        form = new FormData();
        form.append('_token', $("[name='_token']").val());
        form.append('url', $("[name='url']").val());
        $.ajax({
            type: 'POST',
            url: "{{ route('add-url-tracking') }}",
            data: form,
            processData: false,
            contentType: false,
            success: function(data){
              getListUrlsTracking();  
              alert(data.message);
            },
            error: function (request, error) {
                console.log(request);
            }
        });
        $('#add_url_id').attr('disabled', false);
      });

      setInterval(() => {
        getListUrlsTracking();
      }, 600000);
    
    });

    function getListUrlsTracking(){
        form = new FormData();
        form.append('_token', $("[name='_token']").val());
        $.ajax({
            type: 'GET',
            url: "{{ route('list-url-tracking') }}",
            data: form,
            processData: false,
            contentType: false,
            success: function(data){
                renderUrlTrackingTable(data['data'])
            },
            error: function (request, error) {
                console.log(request);
            }
        });
      }

      function renderUrlTrackingTable(data){
        render_line = '';
        for(let i=0 ; i < data.length ; i++){
          render_line +=  '<tr>' +
                    '<th scope="row">' + data[i]['id'] + '</th>' +
                    '<td>' + data[i]['url'] + '</td>' +
                    '<td>' + data[i]['status_verified_at'] + '</td>' +
                    '<td>' + data[i]['status_code'] + '</td>' +
                    '<td>' + data[i]['body'] + '</td>' +
                    '<td><i id="delete-url" index="' + data[i]['id'] + '" onclick="deleteUrl(' + data[i]['id'] + ')" class="bi bi-trash-fill"></i></td>' +
                    '</tr>';
        }
        $('#tb-url-tracking').html(render_line);
      }

    function deleteUrl(index){
        var url = '{{ route("delete-url-tracking", ":id") }}';
        url = url.replace(':id', index);
        $.ajax({
          headers: { 'X-CSRF-TOKEN': $("[name='_token']").val() },
          type: 'DELETE',
          url: url,
          success: function(data){
            alert('URL deletada!!')
            getListUrlsTracking();
          },
          error: function (request, error) {
            console.log(request);
            console.log(error);
          }
        });
      }
    
    </script>

</x-app-layout>
