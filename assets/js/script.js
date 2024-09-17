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
    // Step 1: Populate first select with top-level categories (e.g., Raw Material)
    var materialSelect = $('#material-select');
    materialSelect.append($('<option>', {
      value: '',
      text: 'Select Material'
    }));

    $.each(data.Material, function (category, subcategories) {
      materialSelect.append($('<option>', {
        value: category,
        text: category
      }));
    });

    // Step 2: Handle selection of first node (e.g., Raw Material)
    materialSelect.change(function () {
      var selectedMaterial = $(this).val();
      if (selectedMaterial) {
        // Clear previous selections for sub-node levels
        clearSubNodeSelects();

        // Check if selected material has sub-categories
        if (data.Material[selectedMaterial] && Object.keys(data.Material[selectedMaterial]).length > 0) {
          var subMaterialSelect = $('#sub-material-select');
          if (subMaterialSelect.length == 0) { // Create new select if not existing
            subMaterialSelect = $('<select>', { id: 'sub-material-select', class: 'form-control mt-3', name: 'material_type', required: 'required' });
            subMaterialSelect.append($('<option>', { value: '', text: 'Select ' + selectedMaterial }));
            $('#material-selection').append(subMaterialSelect);
          } else {
            // If exists, just clear previous options
            subMaterialSelect.empty();
            subMaterialSelect.append($('<option>', { value: '', text: 'Select ' + selectedMaterial }));
          }

          $.each(data.Material[selectedMaterial], function (sub, options) {
            subMaterialSelect.append($('<option>', {
              value: sub,
              text: sub
            }));
          });

          // Step 3: Handle selection of second node (e.g., Aluminum, ALClad, etc.)
          subMaterialSelect.off('change').change(function () { // Remove previous handlers and add new one
            var selectedSubMaterial = $(this).val();
            var subNodeOptions = data.Material[selectedMaterial][selectedSubMaterial];

            if (subNodeOptions && Object.keys(subNodeOptions).length > 0) {
              // Clear only sub-node selects, keep sub-material select
              clearSubNodeSelects();

              let subSelectionDivs = $('<div>', { class: 'subSelectionDivs' });
              let subSelectionDiv = $('<div>', { class: 'subSelectionDiv' });
              $('#material-selection').append(``);
              // Create selects for each sub-node and populate with options
              $.each(subNodeOptions, function (node, nodeOptions) {
                let node_lw = node.toLowerCase();
                var nodeSelect = $('<select>', { id: node_lw, name: node_lw + '[]', class: 'form-control mt-3 sub-node-select', required: 'required' });
                nodeSelect.append($('<option>', { value: '', text: 'Select ' + node }));

                $.each(nodeOptions, function (index, option) {
                  nodeSelect.append($('<option>', {
                    value: option,
                    text: option
                  }));
                });

                subSelectionDiv.append(nodeSelect);
                // checkSubSelections();
              });
              if (page == 'select-materials') {
                let buttonsDiv = $('<div>', { class: 'd-flex justify-content-between py-3 align-items-center border-bottom' });
                buttonsDiv.html(`<button class="btn btn-danger" onclick="removeSubSelection(event)" type="button">Remove</button><button class="btn btn-primary" onclick="addSubSelection(event)" type="button">Add</button>`);
                subSelectionDiv.append(buttonsDiv);
                subSelectionDivs.append(subSelectionDiv);
                // Append new sub-node select
                $('#material-selection').append(subSelectionDivs);
              }else{
                let buttonsDiv = $('<div>', { class: 'd-flex justify-content-between py-3 align-items-center border-bottom' });
                buttonsDiv.html(``);
                subSelectionDiv.append(buttonsDiv);
                subSelectionDivs.append(subSelectionDiv);
                // Append new sub-node select
                $('#material-selection').append(subSelectionDivs);
              }
            } else {
              // No sub-nodes, clear any existing sub-node selects
              clearSubNodeSelects();
            }
          });
        } else {
          // No sub-categories, clear any existing sub-material and sub-node selects
          clearSubSelects();
        }
      }
    });

    // Function to clear all sub-material and sub-node selects
    function clearSubSelects() {
      $('#sub-material-select').remove();
      clearSubNodeSelects();
    }

    // Function to clear only sub-node selects
    function clearSubNodeSelects() {
      $('.subSelectionDivs').remove();
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
