export default class Config {
    private static instance: Config | null = null;

    public readonly baseUrl: string;
    public readonly userUrl: string;
    public readonly petrinetUrl: string;
    public readonly sessionUrl: string;

    public constructor() {
        this.baseUrl = API_URL;
        this.userUrl = "user";
        this.petrinetUrl = "petrinet";
        this.sessionUrl = "session";
    }

    public static getInstance() {
        if (!Config.instance) {
            Config.instance = new Config();
        }
        return Config.instance;
    }
}
