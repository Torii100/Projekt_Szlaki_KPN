<div>
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div id="modal-body" class="modal-content">
            <div class="modal-header">
                <span class="close" id="closeModal">&times;</span>
                <h2 id="modalOperation"></h2>
            </div>
            <div class="modal-body">
                <div style="display: none">
                    <label for="add-id">Id</label>
                    <input type="number" name="modal-id" id="modal-id"/>
                </div>
                <div>
                    <label for="add-name">Nazwa Punktu</label>
                    <input type="text" name="modal-name" id="modal-name"/>
                </div>
                <div>
                    <label for="add-latitude">Szerokość Geograficzna</label>
                    <input type="number" min="-90" max="90" step="any" name="modal-latitude" id="modal-latitude"
                    />
                </div>
                <div>
                    <label for="add-longitude">Długość Geograficzna</label>
                    <input type="number" name="modal-longitude" step="any" id="modal-longitude"/>
                </div>
                <div>
                    <label for="add-source">Wysokość Punktu</label>
                    <input type="number" min="0" name="modal-height" id="modal-height"/>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" id="submitBtn" value="Wykonaj operacje"/>
            </div>
        </div>
    </div>
</div>