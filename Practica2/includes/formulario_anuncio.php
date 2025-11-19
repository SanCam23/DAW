<form action="#" method="post">
    <fieldset>
        <legend>Datos del anuncio</legend>

        <label for="titulo">Título*:</label><br>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required><br><br>

        <label for="descripcion">Descripción*:</label><br>
        <textarea id="descripcion" name="descripcion" rows="5" cols="40" required><?php echo htmlspecialchars($texto); ?></textarea><br><br>

        <label for="precio">Precio (€)*:</label><br>
        <input type="number" id="precio" name="precio" min="0" step="0.01" value="<?php echo htmlspecialchars($precio); ?>" required><br><br>

        <label for="ciudad">Ciudad*:</label><br>
        <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($ciudad); ?>" required><br><br>

        <label for="pais">País*:</label><br>
        <select id="pais" name="pais" required>
            <option value="">Seleccione...</option>
            <?php foreach ($paises as $p): ?>
                <option value="<?php echo $p['IdPais']; ?>" <?php echo ($pais == $p['IdPais']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($p['NomPais']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="tipo_anuncio">Tipo de anuncio*:</label><br>
        <select id="tipo_anuncio" name="tipo_anuncio" required>
            <option value="">Seleccione...</option>
            <?php foreach ($tipos_anuncio as $ta): ?>
                <option value="<?php echo $ta['IdTAnuncio']; ?>" <?php echo ($tipo_anuncio == $ta['IdTAnuncio']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($ta['NomTAnuncio']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="tipo_vivienda">Tipo de vivienda*:</label><br>
        <select id="tipo_vivienda" name="tipo_vivienda" required>
            <option value="">Seleccione...</option>
            <?php foreach ($tipos_vivienda as $tv): ?>
                <option value="<?php echo $tv['IdTVivienda']; ?>" <?php echo ($tipo_vivienda == $tv['IdTVivienda']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tv['NomTVivienda']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit"><?php echo $texto_boton; ?></button>
    </fieldset>
</form>