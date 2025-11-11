const unrar = require('node-unrar-js');
const fs = require('fs');
const path = require('path');

async function extractRar() {
  try {
    const buf = Uint8Array.from(fs.readFileSync('/vercel/sandbox/uploads/mercado-online.rar')).buffer;
    const extractor = await unrar.createExtractorFromData({ data: buf });
    const list = extractor.getFileList();
    const fileHeaders = list.fileHeaders;
    
    for (const header of fileHeaders) {
      if (!header.flags.directory) {
        const extracted = extractor.extract({ files: [header.name] });
        const files = [...extracted.files];
        
        if (files.length > 0 && files[0].extraction) {
          const fullPath = path.join('/vercel/sandbox/mercado-online', header.name);
          const dir = path.dirname(fullPath);
          fs.mkdirSync(dir, { recursive: true });
          fs.writeFileSync(fullPath, Buffer.from(files[0].extraction));
          console.log('Extracted:', header.name);
        }
      }
    }
    console.log('Extraction complete!');
  } catch (error) {
    console.error('Error:', error);
  }
}

extractRar();
