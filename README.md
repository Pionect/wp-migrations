wp-migrations

**Probleem:** 
Bij het door-ontwikkelen van een website zijn er geregeld opties die ingesteld moeten worden in plugins. Wanneer deze op een lokale omgeving zijn ingesteld kost het veel aandacht om deze opties goed live te krijgen, op bij een andere developer. 

**Oplossing:**
Een plugin ontwikkelen die migrations ondersteund geinspireerd op de migrations van Laravel. Bij het ontwikkelen van een site zou deze plugin geinstalleerd kunnen worden en kunnen er in een separate plugin migrations geschreven worden.

Een migration kan de volgende acties uitvoeren:
– installeren van plugin
– updaten van plugin
– verwijderen van plugin
– inserten van een wp_option
– updaten van een wp_option

De plugin acties zouden afhankelijk mogen zijn van wp-cli

De wp_option classes zouden in een folder verzameld moeten worden. Deze folder zou vanuit de custom plugin geregistreerd kunnen worden bij de WP_migrations plugin zodat deze alle migrations indexeerd en indien nodig nog uitvoert. Een enkele implementatie van OptionMigration zou er als volgt uit kunnen zien:

    function handle($option_value){
        $obj = unserialize($option_value);
        $obj->option_x = true;
        return serialize($obj);
    }