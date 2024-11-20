// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()

$(document).ready(function () {
    // Load JSON data
    $.getJSON('data.json', function (data) {
        var materialSelectContainer = $('#material-selection');
        var materialGrid = $('<div>', { class: 'grid-container' });
        materialSelectContainer.append(materialGrid);

        // Display raw materials as radio buttons
        $.each(data.Material, function (material, subcategories) {
            var materialDiv = $('<div>', { class: 'form-check' });
            var materialRadio = $('<input>', {
                type: 'radio',
                class: 'form-check-input',
                id: material,
                name: 'material_standard',
                value: material,
            });
            var materialLabel = $('<label>', {
                class: 'form-check-label',
                for: material,
                text: material,
            });

            materialDiv.append(materialRadio).append(materialLabel);
            materialGrid.append(materialDiv);
        });

        // Handle Raw Material selection
        materialSelectContainer.off('change').on('change', "input[name='material_standard']", function () {
            var selectedMaterial = $("input[name='material_standard']:checked").val();
            clearSubSections();

            if (data.Material[selectedMaterial] && Object.keys(data.Material[selectedMaterial]).length > 0) {
                var subMaterialContainer = $('#sub-material-select');
                if (subMaterialContainer.length === 0) {
                    subMaterialContainer = $('<div>', { id: 'sub-material-select', class: 'form-group mt-3' });
                    materialSelectContainer.append(subMaterialContainer);
                }

                // Label for Sub-Material selection
                var subMaterialLabel = $('<h4>').text("Select Sub-Material");
                subMaterialContainer.append(subMaterialLabel);

                var subMaterialGrid = $('<div>', { class: 'grid-container' });

                $.each(data.Material[selectedMaterial], function (subMaterial, options) {
                    var subMaterialDiv = $('<div>', { class: 'form-check' });
                    var subMaterialRadio = $('<input>', {
                        type: 'radio',
                        class: 'form-check-input',
                        id: subMaterial,
                        name: 'sub_material',
                        value: subMaterial,
                    });
                    var subMaterialLabel = $('<label>', {
                        class: 'form-check-label',
                        for: subMaterial,
                        text: subMaterial,
                    });

                    subMaterialDiv.append(subMaterialRadio).append(subMaterialLabel);
                    subMaterialGrid.append(subMaterialDiv);
                });

                subMaterialContainer.append(subMaterialGrid);

                // Handle nested selections (Alloy, Condition, Form)
                subMaterialContainer.off('change').on('change', "input[name='sub_material']", function () {
                    var selectedSubMaterial = $("input[name='sub_material']:checked").val();
                    clearNestedSections();

                    if (data.Material[selectedMaterial][selectedSubMaterial]) {
                        let nestedSelectionContainer = $('<div>', { class: 'nestedSelectionContainer' });

                        $.each(data.Material[selectedMaterial][selectedSubMaterial], function (nodeType, nodeOptions) {
                            var nodeLabel = $('<h5>').text("Select " + nodeType);
                            nestedSelectionContainer.append(nodeLabel);

                            var nodeGrid = $('<div>', { class: 'grid-container' });

                            nodeOptions.forEach(function (option) {
                                var optionDiv = $('<div>', { class: 'form-check' });
                                var optionRadio = $('<input>', {
                                    type: 'radio',
                                    class: 'form-check-input',
                                    id: option,
                                    name: nodeType.toLowerCase(),
                                    value: option,
                                });
                                var optionLabel = $('<label>', {
                                    class: 'form-check-label',
                                    for: option,
                                    text: option,
                                });

                                optionDiv.append(optionRadio).append(optionLabel);
                                nodeGrid.append(optionDiv);
                            });

                            nestedSelectionContainer.append(nodeGrid);
                        });

                        materialSelectContainer.append(nestedSelectionContainer);
                    }
                });
            }
        });

        // Clear the sub-material and nested selections
        function clearSubSections() {
            $('#sub-material-select').remove();
            clearNestedSections();
        }

        function clearNestedSections() {
            $('.nestedSelectionContainer').remove();
        }
    });
});

function checkFormValidity() {
  $('#submit-btn').prop('disabled', false);
  $('#material-selection select').each(function () {
    if ($(this).val() === '') {
      $('#submit-btn').prop('disabled', true);
    }
  });
}

// function checkSubSelections() {
//   let subSelections = $('.subSelectionDiv');
//   if(subSelections.length <= 1){
//     subSelections.remove();
//   }
// }

function addSubSelection(e) {
  let target = e.target;

  // Find the closest '.subSelectionDiv' element
  let elementToCopy = $(target).closest('.subSelectionDiv');

  // Clone the element
  let clonedElement = elementToCopy.clone();

  // Append the cloned element to the '.subSelectionDivs' container
  $('.subSelectionDivs').append(clonedElement);
}

function removeSubSelection(e) {
  let target = e.target;
  if ($('.subSelectionDiv').length > 1) {
    // Find the closest '.subSelectionDiv' element
    let elementToDelete = $(target).closest('.subSelectionDiv');
    elementToDelete.remove();
  }
}
