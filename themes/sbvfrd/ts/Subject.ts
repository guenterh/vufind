import {Detail} from "./Detail";

export class Subject implements Detail {
    public hasSufficientData: boolean;
    public type: string;
    public id: string;
    public name: string;
}
