/// <reference path='./index.ts'/>
/// <reference path='../Models/Settings.ts'/>
/// <reference path='../Models/Store.ts'/>
/// <reference path='../../vendor/Definitions/Hashtable.d.ts'/>

class State extends SystemElement implements IHashtableOptions<State>
{
    public replaceDuplicateKey = true;

    protected Id : number;
    protected Map : IHashtable<string, TokenCount>;

    public constructor(id : number)
    {
        super(id);
        this.Map = new Hashtable<string, TokenCount>();
        // assign zero to every place
        let petrinet = Store.GetInstance().GetPetrinet();
        if(petrinet != null) {
            let places = petrinet.GetPlaces();
            for(let i = 0; i < places.length; i++){
                this.Add(places[i], 0);
            }
        }
    }

    public Add(place : string, tokens : number | TokenCount)
    {
        if(typeof tokens == "number") tokens = new IntToken(tokens);
        this.Map.put(place, tokens);
    }

    public GetPlace(place : string)
    {
        if(!this.Map.containsKey(place)) return undefined;
        return this.Map.get(place);
    }

    public ToDisplayString()
    {
        let settings = Settings.GetInstance();
        let style = settings.GetStateDisplayStyle();

        let keys = this.Map.keys();
        keys.sort();
        let elems = [];
        for (let i = 0; i < keys.length; i++)
        {
            let key = keys[i];
            let val = this.Map.get(key);
            // skip zero's when enabled
            if(val instanceof IntToken && val.value ==0 && style == StateDisplayStyle.NON_NEGATIVE)
                continue;
            let s = key + ":" + this.Map.get(key).ToString();
            elems.push(s);
        }
        let res = elems.join(" ");
        return res;
    }

    public ToSystemString()
    {
        let keys = this.Map.keys();
        keys.sort();
        let elems = [];
        for(let i = 0; i < keys.length; i++)
        {
            let key = keys[i];
            let val = this.Map.get(key);
            if(val instanceof IntToken && val.value == 0)
                continue;
            let s = key + ":" + this.Map.get(key).ToString();
            elems.push(s);
        }
        let res = elems.join(", ");
        return res;
    }

    public SetId(id : number) 
    {
        this.Id = id;
    }

    public GetMap()
    {
        return this.Map;
    }

    public equals(other : State)
    {
        return this.Id == other.Id;
    }

    public hashCode()
    {
        return this.Id;
    }
}