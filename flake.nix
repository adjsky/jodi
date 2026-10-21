{
  description = "jodi";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    utils.url = "github:numtide/flake-utils";
  };

  outputs =
    {
      nixpkgs,
      utils,
      ...
    }:
    utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = import nixpkgs {
          inherit system;
        };
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = with pkgs; [
            nodejs_26
            php84
            php84Packages.composer
            just
            frankenphp
            commitizen
          ];
          shellHook = ''
            export COREPACK_SHIMS="$PWD/.corepack/bin"

            mkdir -p "$COREPACK_SHIMS"
            corepack enable npm --install-directory "$COREPACK_SHIMS"

            export PATH="$COREPACK_SHIMS:$PATH"
          '';
        };
      }
    );
}
