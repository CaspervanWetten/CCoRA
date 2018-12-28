/// <reference path='./index.ts'/>

class IntToken extends TokenCount
{
    public value;
    public constructor(val : number)
    {
        super();
        this.value = val;
    }

    public Add(i : number | TokenCount)
    {
        if(typeof i == "number"){
            return new IntToken(i + this.value);
        }
        else if(i instanceof IntToken) {
            return new IntToken(this.value + i.value);
        }
        return new OmegaToken();
    }

    public Subtract(i : number | TokenCount)
    {
        if(typeof i == "number") {
            return new IntToken(this.value - i);
        }
        else if(i instanceof IntToken) {
            return new IntToken(this.value + i.value);
        }
        return new OmegaToken();
    }

    public ToString() : string
    {
        return this.value.toString();
    }
}