<div class="space-y-6">
    <!-- File Upload Area -->
    <div 
        class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors"
        :class="{ 'border-blue-400 bg-blue-50': isDragOver }"
        @dragover.prevent="isDragOver = true"
        @dragleave.prevent="isDragOver = false" 
        @drop.prevent="
            isDragOver = false;
            let files = $event.dataTransfer.files;
            if (files.length > 0) {
                csvFile = files[0];
            }
        "
    >
        <div x-show="!csvFile">
            <x-icons.document-arrow-up class="mx-auto h-12 w-12 text-gray-400" />
            <div class="mt-4">
                <label for="csv-file" class="cursor-pointer">
                    <span class="mt-2 block text-sm font-medium text-gray-900">
                        Glissez-déposez votre fichier CSV ici, ou 
                        <span class="text-blue-600 hover:text-blue-500">cliquez pour parcourir</span>
                    </span>
                </label>
                <input 
                    id="csv-file" 
                    name="csv-file" 
                    type="file" 
                    accept=".csv" 
                    class="sr-only"
                    @change="csvFile = $event.target.files[0]"
                >
            </div>
            <p class="mt-1 text-xs text-gray-500">
                Fichiers CSV uniquement, 10 MB max
            </p>
        </div>

        <!-- File Selected -->
        <div x-show="csvFile" class="space-y-4">
            <div class="flex items-center justify-center space-x-2">
                <x-icons.document-arrow-up class="h-8 w-8 text-green-500" />
                <div class="text-left">
                    <p class="text-sm font-medium text-gray-900" x-text="csvFile?.name"></p>
                    <p class="text-xs text-gray-500" x-text="csvFile ? (csvFile.size / 1024 / 1024).toFixed(2) + ' MB' : ''"></p>
                </div>
            </div>
            
            <button 
                type="button"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                @click="csvFile = null; document.getElementById('csv-file').value = ''"
            >
                <x-icons.x-mark class="h-3 w-3 mr-1" />
                Supprimer
            </button>
        </div>
    </div>

    <!-- CSV Format Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <x-icons.information-circle class="h-5 w-5 text-blue-400" />
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Format CSV requis</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Séparateur : point-virgule (;)</li>
                        <li>Colonnes : Nom;Description;Date;Étiquettes</li>
                        <li>Date : format YYYY-MM-DD</li>
                        <li>Étiquettes : séparées par des virgules</li>
                        <li>Maximum : 1000 lignes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3">
        <button 
            type="button"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            @click="$dialog.close(); csvFile = null"
        >
            Annuler
        </button>
        <button 
            type="button"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!csvFile"
            @click="alert('Import CSV functionality will be implemented in the next step!')"
        >
            Importer
        </button>
    </div>
</div>
