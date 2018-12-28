/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>
/// <reference path='../../../vendor/Definitions/Hashtable.d.ts'/>

class Edge extends SystemElement implements IHashtableOptions<Edge>
{
    public replaceDuplicateKey = true;

    protected From          : State;
    protected To            : State;
    protected Transition    : string;

    protected Id            : number;

    public constructor(id : number, from : State ,to : State, transition : string)
    {
        super(id);

        this.From       = from;
        this.To         = to;
        this.Transition = transition;
    }

    public ToString()
    {
        let s = "Edge from: " + this.From.ToDisplayString() + ", to: " + this.To.ToDisplayString();
        return s;
    }

    //#region Getters and Setters
    public GetFromState()
    {
        return this.From;
    }

    public GetToState()
    {
        return this.To;
    }

    public GetTransition()
    {
        return this.Transition;
    }

    public SetFromState(state : State)
    {
        this.From = state;
    }

    public SetToState(state : State)
    {
        this.To = state;
    }

    public SetTransition(trans : Transition)
    {
        this.Transition = trans;
    }
    //#endregion

    //#region hashtable functionality
    public equals(other : Edge)
    {
        return this.ToString() === other.ToString();
    }

    public hashCode()
    {
        return this.ToString();
    }
    //#endregion
}