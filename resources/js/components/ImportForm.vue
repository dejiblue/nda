<template>
    <div class="container">
            <div id="importSection" class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Import Contacts</div>
                        @if ($toImport > 0)
                            <p>Contacts left to import: {{ $import_remainder }}</p>
                        @endif
                        <div class="card-body">
                            <p>{{ session('status') }}</p>
                            <form @submit="formSubmit" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                                    <label for="file" class="control-label">CSV file to import</label>
                                    <input id="file" type="file" class="form-control" name="file" required>
                                </div>
                                <p><button type="submit" class="btn btn-success" name="submit"><i class="fa fa-check"></i> Submit</button></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>

    export default {

        mounted() {
            console.log('Component mounted.')
        },

        data() {

            return {

              file: '',

              success: ''

            };

        },

        methods: {

            onFileChange(e){

                console.log(e.target.files[0]);

                this.file = e.target.files[0];

            },

            formSubmit(e) {

                e.preventDefault();

                let currentObj = this;



                const config = {

                    headers: { 'content-type': 'multipart/form-data' }

                }



                let formData = new FormData();

                formData.append('file', this.file);



                axios.post('/staff/import', formData, config)

                .then(function (response) {

                    currentObj.success = response.data.success;

                })

                .catch(function (error) {

                    currentObj.output = error;

                });

            }

        }

    }

</script>