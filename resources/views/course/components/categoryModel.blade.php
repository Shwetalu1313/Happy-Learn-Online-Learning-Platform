{{--Large Modal--}}

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Choose Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{--<div class="mb-3">
                    <label for="category" class="form-label">{{__('cate.cate')}}</label>
                    <select class="form-select" aria-label="Choose Sub-Category" id="category" name="cate_select">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $loop->iteration }}. {{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('cate_select')
                    <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>--}}

                <div class="mb-3">
                    <label for="sub_category" class="form-label">{{__('cate.sub_cate')}}</label>
                    <select class="form-select" aria-label="Choose Sub-Category" id="sub_category" name="sub_cate_select">
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}">{{ $loop->iteration }}. {{ $subcategory->name }}</option>
                        @endforeach
                    </select>
                    @error('sub_cate_select')
                    <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"><i class="bi bi-hand-thumbs-up me-3"></i>Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get references to the category and subcategory select dropdowns
        const categorySelect = document.getElementById('category');
        const subcategorySelect = document.getElementById('sub_category');
        const inputField = document.getElementById('sub_cate');

        // Function to handle copying selected subcategory text to the input field
        function copySubcategoryText() {
            // Get the selected subcategory option
            const selectedOption = subcategorySelect.options[subcategorySelect.selectedIndex];
            // Copy the text of the selected subcategory to the input field
            inputField.value = selectedOption.text;
            console.log(subcategorySelect.value);
        }

        const okButton = document.querySelector('#staticBackdrop .modal-footer button.btn-primary');
        okButton.addEventListener('click', function() {
            // Copy the selected subcategory text to the input field
            copySubcategoryText();
        });
    });


</script>


