import Avatar from "./ui/Avatar.svelte";
import InfoActionRow from "./ui/InfoActionRow.svelte";
import InfoBlock from "./ui/InfoBlock.svelte";
import InfoModalRow from "./ui/InfoModalRow.svelte";

export const User = {
    Avatar,
    Info: {
        Block: InfoBlock,
        ModalRow: InfoModalRow,
        ActionRow: InfoActionRow
    }
};
